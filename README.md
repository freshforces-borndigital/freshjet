### Summary

- [What is Freshjet?](#about)
- [Installation](#installation)
- [Usage](#usage)
  - [Simple mail](simple-mail)
  - [Bulk mail](bulk-mail)
- [Contributing](#contributing)
- [License](#license)

### About

Freshjet is a WordPress plugin of [Mailjet](https://www.mailjet.com/) implementation for [Fresh Forces](https://github.com/freshforces-borndigital/). This plugin is very simple, giving you drop-in replacement for `wp_mail` plus one flexible function: `bulk_mail`

### Installation

- Download the [latest release](https://github.com/freshforces-borndigital/freshjet/releases/latest).
- Extract and rename it as *freshjet*.
- Upload to your *wp-content*
- Activate it from Dashboard -> Plugins

### Usage

There are 3 ways to use *simple mail* (using `wp_mail`) and *bulk mail* (using `bulk_mail`). 

Lets provide them with the same `$subject` & `$body`:
```
$subject = 'Testing simple email';
$body    = '<p>Testing content for <span style="font-weight: 700;">simple email</span></p>'
```

#### Simple mail

Simple mail (via `wp_mail`) is usually suitable for sending simple emails. But not only that, you can also send mass emails with the same content (If you want dynamic content, please use [`bulk_mail`](bulk-mail)).

> Note:
> Our support for `wp_mail` parameter is still limited to `$recipient`, `$subject`, `$body`, & `$headers`.
>
> Currently, the `$attachments` parameter is not supported. We want to add it in future. But if you want to add it sooner, please send us PR. We will be happy to check & merge it :)

**Simple mail - 1st way**

The most simple way:

```
<?php
$result = wp_mail('someone@domain.com', $subject, $body);
```

**Simple mail - 2nd way**

Add array of emails as recipient:

```
<?php
$emails = ['someone1@domain.com', 'someone2@domain.com'];
$result = wp_mail($emails, $subject, $body);
```

**Simple mail - 3rd way**

You can specify the name of each email using this way:

```
<?php
$recipients = [
  [
    'Name'  => 'Mr. Bagus', 
    'Email' => 'contactjavas@gmail.com'
  ],
  [
    'Name'  => 'Mr. Prast',
    'Email' => 'prast@fresh-forces.com'
  ]
];
$result = wp_mail($recipients, $subject, $body);
```

#### Bulk mail

**Bulk mail - 1st way**

With this 1st way, you can separate recipients to receive different subject & body.

```
<?php
$param = [
  [
    'recipient' => 'someone@domain.com',
    'subject'   => $subject,
    'body'      => $body
  ],
  [
    'recipient' => 'someone2@domain.com',
    'subject'   => $subject,
    'body'      => $body
  ]
];
$result = bulk_mail($param);
```

**Bulk mail - 2nd way**

And you can add group of emails to receive specific subject & body using this 2nd way:

```
<?php
$param = [
  [
    'recipient' => ['someone@domain.com', 'someone2@domain.com'],
    'subject'   => $subject,
    'body'      => $body
  ],
  [
    'recipient' => ['someone3@domain.com', 'someone4@domain.com'],
    'subject'   => $subject,
    'body'      => $body
  ]
];
$result = bulk_mail($param);
```

**Bulk mail - 3rd way**

You can also specify the name of each email with this 3rd way:

```
<?php
$param = [
  [
    'recipient' => [
      [
        'Name' => 'Someone 1',
        'Email' => 'someone@domain.com'
      ],
      [
        'Name' => 'Someone 2',
        'Email' => 'someone2@domain.com'
      ]
    ],
    'subject' => $subject,
    'body'    => $body
  ],
  [
    'recipient' => [
      [
        'Name' => 'Someone 3',
        'Email' => 'someone3@domain.com'
      ],
      [
        'Name' => 'Someone 4',
        'Email' => 'someone4@domain.com'
      ]
    ],
    'subject' => $subject,
    'body'    => $body
  ]
];
$result = bulk_mail($param);
```

### Contributing

Support us by submitting issue or sending PR

### License

Licensed under the [MIT License](https://oss.ninja/mit?organization=Fresh%20Forces) by [Fresh Forces](https://github.com/freshforces-borndigital/)