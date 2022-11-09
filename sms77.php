<?php

class sms77 extends PluginBase {
    static protected $description = 'Send SMS via sms77';
    static protected $name = 'sms77';
    protected $settings = [
        'apiKey' => [
            'help' => 'Get yours @ https://app.sms77.io/developer',
            'htmlOptions' => ['required' => 'required'],
            'label' => 'API Key',
            'type' => 'password',
        ],
        'attributeField' => [
            'default' => 'attribute_1',
            'help' => 'The attribute field for the mobile phone number',
            'htmlOptions' => ['required' => 'required'],
            'label' => 'Attribute Field',
            'type' => 'string',
        ],
        'email' => [
            'default' => 0,
            'help' => 'Enabling this setting will send both SMS and email',
            'label' => 'Send Email',
            'type' => 'boolean',
        ],
        'enabled' => [
            'default' => [],
            'help' => 'Defines on which events the SMS plugin should be activated',
            'htmlOptions' => ['multiple' => 'multiple'],
            'label' => 'Event Types',
            'options' => [
                'invite' => 'Invitation',
                'remind' => 'Reminder',
            ],
            'type' => 'select',
        ],
        'flash' => [
            'default' => 0,
            'help' => 'Depending on the device, the SMS gets displayed directly in the display and won\'t get saved',
            'label' => 'Flash',
            'type' => 'boolean',
        ],
        'foreign_id' => [
            'default' => '',
            'help' => 'Optional foreign identifier returned in DLR callbacks',
            'label' => 'Foreign ID',
            'htmlOptions' => ['maxlength' => 64],
            'type' => 'string',
        ],
        'from' => [
            'default' => '',
            'help' => 'Value displayed as the SMS sender',
            'label' => 'From',
            'htmlOptions' => ['maxlength' => 16],
            'type' => 'string',
        ],
        'label' => [
            'default' => '',
            'help' => 'Optional label for your statistics',
            'label' => 'Label',
            'htmlOptions' => ['maxlength' => 100],
            'type' => 'string',
        ],
        'performance_tracking' => [
            'default' => 0,
            'help' => 'Enable Performance Tracking for URLs found in the message text.',
            'label' => 'Performance Tracking',
            'type' => 'boolean',
        ],
        'text' => [
            'default' => 'Dear {FIRSTNAME} {LASTNAME},' . PHP_EOL
                . 'we invite you to participate in the survey below:' . PHP_EOL
                . '{SURVEY_URL}' . PHP_EOL
                . 'Survey Team',
            'help' => 'You may use the placeholders '
                . '{EMAIL}, {FIRSTNAME}, {LASTNAME} and {SURVEY_URL}',
            'htmlOptions' => ['maxlength' => 1520, 'rows' => 7],
            'label' => 'SMS Text',
            'type' => 'text',
        ],
    ];
    protected $storage = 'DbStorage';

    /**
     * @return void
     */
    public function init() {
        $this->subscribe('beforeSurveySettings');
        $this->subscribe('beforeTokenEmail');
        $this->subscribe('newSurveySettings');
    }

    /**
     * @param string $token
     * @param string $surveyId
     * @return string
     */
    private function buildSurveyLink($token, $surveyId) {
        $protocol = 'http';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') $protocol .= 's';

        return $protocol . '://' . $_SERVER['SERVER_NAME']
            . '/index.php/survey/index/sid/' . $surveyId . '/token/' . $token;
    }

    /**
     * @param array $tokenData
     * @param string $surveyId
     * @return string
     */
    private function buildMessage(array $tokenData, $surveyId) {
        return str_replace(
            [
                '{EMAIL}',
                '{FIRSTNAME}',
                '{LASTNAME}',
                '{SURVEY_URL}',
            ],
            [
                (string)$tokenData['email'],
                (string)$tokenData['firstname'],
                (string)$tokenData['lastname'],
                $this->buildSurveyLink($tokenData['token'], $surveyId),
            ],
            $this->get(
                'text',
                'Survey',
                $surveyId,
                $this->settings['text']['default']
            )
        );
    }

    /**
     * This function handles sending SMS messages
     * If it's an email invite, it doesn't interfere and keeps the settings as they are
     * @return void
     */
    public function beforeTokenEmail() {
        $event = $this->getEvent();
        if (!$event) return;

        $surveyId = (string)$event->get('survey');
        $enabled = (array)$this->get('enabled', 'Survey', $surveyId);
        $emailType = $event->get('type');

        if (!in_array($emailType, $enabled)) return;

        $tokenData = $event->get('token');
        $attributeField = $this->get('attributeField');

        if (!isset($tokenData[$attributeField])) {
            echo $this->gT('sms77 plugin is enabled.')
                . $this->gT('If you do not wish to send SMS invitations, disable it')
                . $this->gT('If you intend to use it, the SMS was not sent.')
                . $this->gT('Add an attribute with the phone number or "NA" for emails.');
            exit;
        }

        $to = (string)$tokenData[$attributeField];

        if (empty($to) || $to === 'NA') return;

        if (!$this->get('email')) $this->event->set('send', false);

        $text = $this->buildMessage($tokenData, $surveyId);

        $res = $this->sms(compact('text', 'to'));
        if (100 !== $res) {
            echo $this->gT('SMS not sent. Please contact an administrator.');
            exit;
        }
    }

    /**
     *  This function handles sending the http request.
     *  Proxy settings should be configured.
     *  The third argument (request_header) is optional
     *  returns the response from the external page/API
     * @param array $payload
     * @return int
     */
    private function sms(array $payload) {
        $apiKey = $this->get('apiKey');

        if (!$apiKey) {
            echo $this->gT('Can not send SMS because of missing sms77 API key.');
            exit;
        }

        exit(var_dump($_REQUEST));

        $flash = $this->get('flash');
        if (!empty($flash)) $payload['flash'] = $flash;

        $foreignId = $this->get('foreign_id');
        if (!empty($foreignId)) $payload['foreign_id'] = $foreignId;

        $from = $this->get('from');
        if (!empty($from)) $payload['from'] = $from;

        $label = $this->get('label');
        if (!empty($label)) $payload['label'] = $label;

        $performanceTracking = $this->get('performance_tracking');
        if (!empty($label)) $payload['performance_tracking'] = $performanceTracking;

        $curlHandle = curl_init('https://gateway.sms77.io/api/sms');
        $options = [
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-type: application/json',
                'SentWith: LimeSurvey',
                'X-Api-Key: ' . $apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
        ];

        curl_setopt_array($curlHandle, $options);

        $response = curl_exec($curlHandle);

        curl_close($curlHandle);

        return (int)(is_bool($response) ? $response : json_decode($response)->success);
    }

    /**
     * This event is fired by the administration panel to gather extra settings
     * available for a survey. These settings override the global settings.
     * The plugin should return setting meta data.
     * @return void
     */
    public function beforeSurveySettings() {
        $settings = $this->settings;
        $surveyId = $this->event->get('survey');

        unset($settings['enabled']['help']);

        $keys = [
            'attributeField',
            'email',
            'enabled',
            'flash',
            'foreign_id',
            'from',
            'label',
            'performance_tracking',
            'text',
        ];
        foreach ($keys as $key) $settings[$key]['current'] = $this->get(
                $key,
                'Survey',
                $surveyId,
                $this->get($key, null, null, $settings[$key]['default'])
            );

        $this->event->set('surveysettings.' . $this->id, [
            'name' => get_class($this),
            'settings' => $settings,
        ]);
    }

    /**
     * This event is fired when survey settings is saved.
     * @return void
     */
    public function newSurveySettings() {
        foreach ($this->event->get('settings') as $k => $v) {
            if (!isset($v) && isset($this->settings[$k]['default']))
                $v = $this->settings[$k]['default'];

            $this->set($k, $v, 'Survey', $this->event->get('survey'));
        }
    }
}
