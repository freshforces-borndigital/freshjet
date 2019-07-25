## Summary

- [What is Freshjet?](#about)
- [Installation](#installation)
- [Usage](#usage)
  - [Simple mail](#simple-mail)
  - [Bulk mail](#bulk-mail)
- [Contributing](#contributing)
- [License](#license)

## About

Freshjet is a [Mailjet](https://www.mailjet.com/) `wp_mail()` drop-in replacement. This plugin is very simple:

- Giving you drop-in replacement for `wp_mail`
- Support for [Mailjet Passport Template](https://app.mailjet.com/templates/transactional)
- Plus one flexible function: `bulk_mail`

## Installation

- Download the [latest release](https://github.com/freshforces-borndigital/freshjet/releases/latest).
- Extract and rename it as _freshjet_.
- Upload to your _wp-content_ directory
- Activate it from Dashboard -> Plugins
- [Visit this page](https://app.mailjet.com/transactional) to see your Mailjet Keys.
- Insert your _API Public Key (SMTP username)_ and _API Secret Key (SMTP password)_ in _yoursite/wp-admin/admin.php?page=freshjet_

## Usage

There are some ways to use `wp_mail` and (additional function) `bulk_mail`. Lets provide them with the same `$subject` & `$body`:

```
$subject = 'Testing email';
$body    = '<p>Testing content via <span style="font-weight: 700;">Mailjet</span></p>'
```

### Simple mail

Simple mail (via `wp_mail`) is usually suitable for sending simple emails. But not only that, you can also send mass emails with the same content (If you want dynamic content, please use [`bulk_mail`](#bulk-mail)).

**1st way**

```
<?php
// The simplest way.
$is_success = wp_mail('someone@domain.com', $subject, $body);
```

**2nd way**

```
<?php
// Add array of emails as recipients.
$recipients = ['someone1@domain.com', 'someone2@domain.com'];
$is_success = wp_mail($recipients, $subject, $body);
```

**3rd way**

```
<?php
// Add comma-separated string of emails as recipients.
$recipients = 'someone1@domain.com, someone2@domain.com';
$is_success = wp_mail($recipients, $subject, $body);
```

**4th way**

```
<?php
// You can specify the name of each recipient using this way.
$recipients = [
    [
        'Name'  => 'Someone 1',
        'Email' => 'someone@domain.com'
    ],
    [
        'Name'  => 'Someone 2',
        'Email' => 'someone2@domain.com'
    ]
];
$result = wp_mail($recipients, $subject, $body);
```

### Bulk mail

**1st way**

```
<?php
// With this 1st way, you can separate recipients to receive different subject & body.
$param = [
    [
        'recipient' => 'someone@domain.com',
        'subject'   => $subject,
        'body'      => $body
    ],
    [
        'recipient' => 'someone2@domain.com',
        'subject'   => 'Another subject',
        'body'      => '<p>Another body</p>'
    ]
];
$result = bulk_mail($param);
```

**2nd way**

```
<?php
// And you can add group of emails to receive specific subject & body using this 2nd way.
$param = [
    [
        'recipient' => ['someone@domain.com', 'someone2@domain.com'],
        'subject'   => $subject,
        'body'      => $body
    ],
    [
        'recipient' => ['someone3@domain.com', 'someone4@domain.com'],
        'subject'   => 'Another subject',
        'body'      => '<p>Another body</p>'
    ]
];
$result = bulk_mail($param);
```

**3rd way**

```
<?php
// And you can add comma-separated string of emails to receive specific subject & body using this 3rd way.
$param = [
    [
        'recipient' => 'someone@domain.com, someone2@domain.com',
        'subject'   => $subject,
        'body'      => $body
    ],
    [
        'recipient' => 'someone3@domain.com, someone4@domain.com',
        'subject'   => 'Another subject',
        'body'      => '<p>Another body</p>'
    ]
];
$result = bulk_mail($param);
```

**4th way**

```
<?php
// You can also specify the name of each email using this 3rd way
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

## Contributing

Support us by submitting issue or sending PR

## License

Licensed under the [GPL-3.0 License](https://oss.ninja/gpl-3.0?organization=Fresh-Forces) by [Fresh Forces](https://github.com/freshforces-borndigital/)
