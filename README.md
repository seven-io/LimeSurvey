![](https://www.seven.io/wp-content/uploads/Logo.svg "seven Logo")

# Plugin for LimeSurvey
This plugin adds the possibility to send SMS for survey invitations and reminders.

## Prerequisites
- Limesurvey CE - only tested with version 5.x
- An [API Key](https://help.seven.io/en/api-key-access) from [seven](https://www.seven.io)

## Installation

1. Download the [latest release](https://github.com/seven-io/LimeSurvey/releases/latest/download/seven-limesurvey-latest.zip)
2. Extract the archive to `/path/to/limesurvey/plugins`
3. Login to the administration, go to `Configuration -> Plugins` and activate the plugin
4. Click on `seven`, go to `Settings`, configure it to your needs and click `Save`

## Usage

### Send SMS invitation to survey participants
1. Create a survey and set it to closed-access mode.
2. Add a participant to the survey.
3. Go to `Survey participants`, click `Manage attributes` and add a field for the mobile phone number.
4. Click the pencil icon in the participant entry row and set a mobile phone in the `Additional attributes` tab.
5. Go to `Simple plugins -> seven`.
6. Make sure that the attribute field matches the name of the mobile phone field which you just created.
7. Make sure that the event type `Invitation` is added.
8. Go to `Survey participants` and click `Generate tokens`.
9. Click `Invitations & reminders` and choose or `Send email invitation` from the dropdown.
10. Edit the form and click `Send invitations`.

### Send SMS reminder to survey participants
The procedure for sending SMS reminders is pretty much the same as for invitations.
You just need to tweak these two steps:
For step 7 make sure that the event type `Reminder` is added.
For step 9 choose `Send email reminder` instead.


## Configuration Options

### API Key
An API key from seven required for sending - create one in your [developer dashboard](https://app.seven.io/developer)

### Attribute Field
Defines the attribute field where the mobile phone number is stored

### Send Email
If enabled, both SMS and email gets sent

### Event Types
Defines on which event type(s) the plugin is activated.
Multiple entries are allowed.
The following events are implemented:
- Invitation
- Reminder

### Flash
Depending on the phone, flash SMS get displayed directly in the display and won't get saved

### SMS Text
Define the SMS text to be sent. 
You can make use of the following placeholders which get replaced with its corresponding value or an empty string:

{EMAIL} => the participant's email address

{FIRSTNAME} => the participant's first name

{LASTNAME} => the participant's last name

{SURVEY_URL} => the survey URL



## Support

Need help? Feel free to [contact us](https://www.seven.io/en/company/contact/).

[![MIT](https://img.shields.io/badge/License-MIT-teal.svg)](LICENSE)
