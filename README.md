<img src="https://www.seven.io/wp-content/uploads/Logo.svg" width="250" />

# SMS Plugin for LimeSurvey

Send SMS invitations and reminders to survey participants via [seven.io](https://www.seven.io).

## How it works

The plugin hooks into LimeSurvey's invitation and reminder system. When you send invitations or reminders, SMS messages are automatically sent to participants with a mobile phone number.

- ✅ No cron jobs required
- ✅ No server access needed after installation
- ✅ Fully managed via web interface

## Requirements

- LimeSurvey CE 5.x or 6.x
- [seven.io](https://www.seven.io) account with API key and credit balance
- File system access for initial installation (FTP/SFTP)

## Installation

1. Download the [latest release](https://github.com/seven-io/LimeSurvey/releases/latest/download/seven-limesurvey-latest.zip)
2. Extract to `/path/to/limesurvey/plugins`
3. Go to `Configuration` → `Plugins` and activate **seven**
4. Enter your API key in the plugin settings

## Setup

### 1. Create a mobile phone attribute

Go to your survey → `Participants` → `Manage attributes` → add a new attribute (e.g. `mobile_number`).

### 2. Configure the plugin

Go to your survey → `Simple plugins` (bottom of sidebar) → **seven**:

| Setting | Description |
|---------|-------------|
| **API Key** | Your seven.io API key ([get one here](https://app.seven.io/developer)) |
| **Attribute Field** | Name of your mobile phone attribute (e.g. `attribute_1`) |
| **Event Types** | Select `Invitation` and/or `Reminder` |
| **Send Email** | Enable to send both SMS and email |
| **From** | Sender name (max 11 alphanumeric or 16 numeric characters) |
| **SMS Text** | Message template with placeholders |

### 3. Add participants

Add participants with their mobile phone number in the attribute field. Use `NA` or leave empty to skip SMS for specific participants.

### 4. Send invitations/reminders

Go to `Participants` → `Invite & remind` → select invitation or reminder type.

## Placeholders

Use these in your SMS text:

| Placeholder | Value |
|-------------|-------|
| `{FIRSTNAME}` | Participant's first name |
| `{LASTNAME}` | Participant's last name |
| `{EMAIL}` | Participant's email |
| `{SURVEY_URL}` | Survey link with access code |

## FAQ

**Do I need server access after installation?**
No. Once installed, everything is configured via the LimeSurvey web interface.

**Do I need cron jobs?**
No. SMS are sent instantly when you use the invite/remind function.

**What if a participant has no phone number?**
Only email will be sent (if enabled). Set the attribute to `NA` or leave it empty.

**Can I send only SMS without email?**
Yes. Disable "Send Email" in the plugin settings.

## Support

Need help? [Contact us](https://www.seven.io/en/company/contact/)

[![MIT](https://img.shields.io/badge/License-MIT-teal.svg)](LICENSE)
