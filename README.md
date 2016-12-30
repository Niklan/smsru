# SMS.ru integration for Drupal

Very basic implementation of SMS.ru API for Drupal with Rules integration.

For now only implemented method is - **send/sms**. If you need another, you can make it by yourself ;)

## Install

- Download, extract and place it as all modules. Better to place this module to /sites/all/modules/**custom**.
- Navigate to modules page and enable it.
- Go to settings _(admin/config/services/smsru)_ and set API key.
  ![Settings](http://i.imgur.com/wVqxixo.png)
  
**Attention!** All numbers must be in worldwide format, without leading + and regional number. F.e. correct number for Russia is starts from +7 or 7, not 8 +8.

// @TODO helper function for supported countries (https://sms.ru/?panel=price&machine=1)

If you familiar with coding, you can use my (regex)[https://regex101.com/r/QHEpxO/1] for Russian numbers.

~~~
^\+?\s?7\s?\(?-?(?# code)[0-9]{3}(?# next 3 digit)\s?\)?-?\s?[0-9]{3}\s?-?(?# next 2 digits)[0-9]{2}\s?-?(?# the last 2 digits)[0-9]{2}
~~~

  
## Rules

1. Create any condition you want.
2. All available SMS.ru actions will be in SMS.RU group.
   ![Rules Action](http://i.imgur.com/PzPZDCs.png)
3. Fill required fields and save it.
4. Enjoy.

## Programmatically

### Send SMS

~~~php
smsru_class()->send_sms('79001234567', 'Hello World');
~~~

Project on [GitHub](https://github.com/Niklan/smsru).
