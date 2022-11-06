<p align="center"><a href="https://slick-pay.com" target="_blank"><img src="https://azimutbscenter.com/logos/slick-pay.png" width="380" height="auto" alt="Slick-Pay Logo"></a></p>

## Description

Laravel package for [Slick-Pay](https://slick-pay.com) Transfer API implementation.

* [Prerequisites](#prerequisites)
* [Installation](#installation)
* [Configuration](#configuration)
    * [sandbox](#sandbox)
    * [public_key](#public_key)
* [How to use?](#how-to-use)
    * [Transfer](#transfer)
    * [BankAccount](#bankaccount)
    * [Receiver](#receiver)
* [More help](#more-help)

## Prerequisites

   - PHP 7.4 or above ;
   - [curl](https://secure.php.net/manual/en/book.curl.php) extension must be enabled ;
   - [Laravel](https://laravel.com) 8.0 or above.

## Installation

Just run this command line :

```sh
composer require slick-pay-algeria/transfer-laravel
```

## Configuration

First of all, you have to publish the pakage config file with the command line :

```sh
php artisan vendor:publish --tag=transfer-config
```

Now, you can find a file **transfer.php** within your project **config** folder.

```php
<?php

return [
    'sandbox'    => true,
    'public_key' => "",
];
```

### sandbox

Will indicate if you want to use a sandbox or live environment (default: true).

### public_key

You can retreive your PUBLIC_KEY from your [slick-pay.com](https://slick-pay.com) dashboard.

## How to use?

### Transfer

By using the **Transfer** class, you will be able to **calculate transfer commision**, **create new payments**, **check payments statuses** identified by their transfer ID, also **get user payment history**.

#### calculateCommission

To calculate transfer service commission, you will use the **calculateCommission** function provided within the **Transfer** Class.

##### Parameters

* **amount:** <**numeric**> (required), the transaction amount in "Dinar algérien" currency, the minimum accepted amount is **100 DA**

##### Examples

Default usage :

```php
<?php

use SlickPay\Transfer\Transfer;

$result = Transfer::calculateCommission(1000);

dd($result);
```

##### Return value

The result will be an array like : 

* **success:** <**integer**>, 0 for false, 1 for true
* **error:** <**integer**>, 0 for false, 1 for true
* **messages:** <**array**>, contains error messages, it will be sent only when **error == 1**
* **response:** <**array**>, it will be sent only when **success == 1**, it contains the API response
    * **amount:** Transfer amount after commission calculated.

#### createPayment

To create any new payment, you will use the **createPayment** function provided within the **Transfer** Class.

##### Parameters

* **returnUrl:** <**string**> (optional), the callback URL that the user will be redirected to after the payment was successfully completed from the payment platform
* **amount:** <**numeric**> (required), the transaction amount in "Dinar algérien" currency, the minimum accepted amount is **100 DA**
* **type:** <**string**> (required), the receiver type used to receiver the amount, can be 'internal' for user bank accounts or 'external' for external receivers.
* **transfer_id:** <**string**> (optional), you can refresh a previous failed transfer with it ID.
* **receiver_uuid:** <**string**> (optional), the receiver account UUID.

> Send a the **receiver_uuid** or create a new receiver account on fly, the fields bellow will be required when no **receiver_uuid** is sent

* **rib:** <**numeric**>, the Account ID of the person that will the receive the amount, will be require when no receiver_uuid is sent.
* **fname:** <**string**>, the First Name of the transfer receiver.
* **lname:** <**string**>, the Last Name of the transfer receiver.
* **email:** <**string**>, the E-mail address of the transfer receiver, required when no receiver_uuid is sent and type is 'external'.
* **phone:** <**string**>, the Phone number of the transfer receiver, required when no receiver_uuid is sent and type is 'external'.
* **address:** <**string**>, the Physical address of the transfer receiver.

##### Examples

Default usage :

```php
<?php

use SlickPay\Transfer\Transfer;

$result = Transfer::createPayment([
    'amount'    => 1000,
    'type'      => "internal",
    'rib'       => "01234567890123456789",
    'fname'     => "Jhon",
    'lname'     => "Doe",
    'address'   => "Jhon Doe Address",
    'returnUrl' =>  "https://my-website.com/thank-you-page",
]);

dd($result);
```

##### Return value

The result will be an array like : 

* **success:** <**integer**>, 0 for false, 1 for true
* **error:** <**integer**>, 0 for false, 1 for true
* **messages:** <**array**>, contains error messages, it will be sent only when **error == 1**
* **response:** <**array**>, it will be sent only when **success == 1**, it contains the API response
    * **transferId:** Payment transfer ID (can be used to check payment status)
    * **redirectUrl:** The redirect url to redirect the client to the payment platform

### paymentStatus

If you would like to check any payment status, you will use the **paymentStatus** provided within the **Transfer** Class.

##### Parameters

* **transferId:** <**number**> (required), Payment transfer ID

##### Examples

Check the example below :

```php
<?php

use SlickPay\Transfer\Transfer;

$result = Transfer::paymentStatus(1);

dd($result);
```

##### Return value

The result will be an array like : 

* **success:** <**integer**>, 0 for false, 1 for true
* **error:** <**integer**>, 0 for false, 1 for true
* **status:** <**string**>, contains payment status, it will be sent only when **success == 1**
* **messages:** <**array**>, contains error messages, it will be sent only when **error == 1**
* **response:** <**array**>, it will be sent only when **success == 1**, it contains the API response
    * **date:** The transaction date (format: Y-m-d H:i:s)
    * **amount:** The transaction amount
    * **orderId:** The order ID provided from the payment platform
    * **orderNumber:** The order N° provided from the payment platform
    * **approvalCode:** The approval code returned from the payment platform
    * **respCode:** The response code returned from the payment platform
    * **pdf:** Download the order details as a PDF file

#### paymentHistory

If you would like to get the user payment history, you will use the **paymentHistory** method provided within the **Transfer** Class.

##### Parameters

* **offset:** <**number**> (optional), define wich offset will be used to paginate the resulted rows.

> **Important:** If no parameter is passed to the **paymentHistory** method, all rows will be returned at once (no pagination made).

##### Example

Here is an example below :

```php
<?php

use SlickPay\Transfer\Transfer;

$result = Transfer::paymentHistory(5);

dd($result);
```

##### Return value

The result will be an array like :

* **success:** <**integer**>, 0 for false, 1 for true
* **error:** <**integer**>, 0 for false, 1 for true
* **messages:** <**array**>, contains error messages, it will be sent only when **error == 1**
* **response:** <**array**>, it will be sent only when **success == 1**, it contains the API response
    * **data:** <**array**>, will contain all the payment rows.
    * **links:** <**array**>, pagination links that can be used to generate a pagination for the user UI.
    * **meta:** <**array**>, will contain all pagination data, such as: **total**, **current_page**, **per_page**...

> **Important:** The indexes **response.meta** and **response.links** will be present only when resulted rows are paginated (pass an integer for paymentHistory).

### BankAccount

By using the **BankAccount** class, you will be able to **create user bank accounts**, **update accounts**, also **get user accounts list**.

#### create

To create a new user bank account, you will use the **create** function provided within the **BankAccount** Class.

##### Parameters

* **params:** <**array**> (required), must contain bank account info.
* **params.title:** <**string**> (required), the Title of the bank account.
* **params.rib:** <**numeric**> (required), the Account ID of the bank account.
* **params.fname:** <**string**> (required), the First Name of the bank account.
* **params.lname:** <**string**> (required), the Last Name of the bank account.
* **params.address:** <**string**> (required), the Physical address of the bank account.

##### Examples

Default usage :

```php
<?php

use SlickPay\Transfer\BankAccount;

$result = BankAccount::create([
    'title'   => "My favorite bank account",
    'rib'     => "01234567890123456789",
    'fname'   => "Jhon",
    'lname'   => "Doe",
    'address' => "Jhon Doe Address",
]);

dd($result);
```

##### Return value

The result will be an array like : 

* **success:** <**integer**>, 0 for false, 1 for true.
* **error:** <**integer**>, 0 for false, 1 for true.
* **messages:** <**array**>, contains error messages, it will be sent only when **error == 1**.
* **response:** <**array**>, the new user account related data attributes.

#### update

To update a user bank account, you will use the **update** function provided within the **BankAccount** Class.

##### Parameters

* **uuid:** <**string**> (required), indicate the bank account uuid to update.
* **params:** <**array**> (required), must contain bank account info.
* **params.title:** <**string**> (required), the Title of the bank account.
* **params.rib:** <**numeric**> (required), the Account ID of the bank account.
* **params.fname:** <**string**> (required), the First Name of the bank account.
* **params.lname:** <**string**> (required), the Last Name of the bank account.
* **params.address:** <**string**> (required), the Physical address of the bank account.

##### Examples

Default usage :

```php
<?php

use SlickPay\Transfer\BankAccount;

$result = BankAccount::update('3386ec0e-...-3abe0101d53f', [
    'title'   => "My new favorite bank account",
    'rib'     => "12345678901234567890",
    'fname'   => "Jane",
    'lname'   => "Doe",
    'address' => "Jane Doe Address",
]);

dd($result);
```

##### Return value

The result will be an array like : 

* **success:** <**integer**>, 0 for false, 1 for true.
* **error:** <**integer**>, 0 for false, 1 for true.
* **messages:** <**array**>, contains error messages, it will be sent only when **error == 1**.
* **response:** <**array**>, the new user account related data attributes.

#### list

If you would like to get the user bank account list, you will use the **list** method provided within the **BankAccount** Class.

##### Parameters

* **offset:** <**number**> (optional), define wich offset will be used to paginate the resulted rows.

> **Important:** If no parameter is passed to the **list** method, all rows will be returned at once (no pagination made).

##### Example

Here is an example below :

```php
<?php

use SlickPay\Transfer\BankAccount;

$result = BankAccount::list(5);

dd($result);
```

##### Return value

The result will be an array like :

* **success:** <**integer**>, 0 for false, 1 for true
* **error:** <**integer**>, 0 for false, 1 for true
* **messages:** <**array**>, contains error messages, it will be sent only when **error == 1**
* **response:** <**array**>, it will be sent only when **success == 1**, it contains the API response
    * **data:** <**array**>, will contain all the payment rows.
    * **links:** <**array**>, pagination links that can be used to generate a pagination for the user UI.
    * **meta:** <**array**>, will contain all pagination data, such as: **total**, **current_page**, **per_page**...

> **Important:** The indexes **response.meta** and **response.links** will be present only when resulted rows are paginated (pass an integer for paymentHistory).

### Receiver

By using the **Receiver** class, you will be able to **create user receiver accounts**, **update accounts**, also **get user receivers list**.

#### create

To create a new user receiver account, you will use the **create** function provided within the **Receiver** Class.

##### Parameters

* **params:** <**array**> (required), must contain receiver info.
* **params.title:** <**string**> (required), the Title of the receiver.
* **params.rib:** <**numeric**> (required), the Account ID of the receiver.
* **params.fname:** <**string**> (required), the First Name of the receiver.
* **params.lname:** <**string**> (required), the Last Name of the receiver.
* **params.email:** <**string**> (optional), the E-mail address of the receiver account.
* **params.phone:** <**string**> (optional), the Phone number of the receiver account.
* **params.address:** <**string**> (required), the Physical address of the receiver.

##### Examples

Default usage :

```php
<?php

use SlickPay\Transfer\Receiver;

$result = Receiver::create([
    'title'   => "My favorite receiver account",
    'rib'     => "01234567890123456789",
    'fname'   => "Jhon",
    'lname'   => "Doe",
    'email'   => "jhon-doe@gmail.com",
    'phone'   => "0660xxxxxx",
    'address' => "Jhon Doe Address",
]);

dd($result);
```

##### Return value

The result will be an array like : 

* **success:** <**integer**>, 0 for false, 1 for true.
* **error:** <**integer**>, 0 for false, 1 for true.
* **messages:** <**array**>, contains error messages, it will be sent only when **error == 1**.
* **response:** <**array**>, the new user account related data attributes.

#### update

To update a user receiver account, you will use the **update** function provided within the **Receiver** Class.

##### Parameters

* **uuid:** <**string**> (required), indicate the receiver uuid to update.
* **params:** <**array**> (required), must contain receiver info.
* **params.title:** <**string**> (required), the Title of the receiver.
* **params.rib:** <**numeric**> (required), the Account ID of the receiver.
* **params.fname:** <**string**> (required), the First Name of the receiver.
* **params.lname:** <**string**> (required), the Last Name of the receiver.
* **params.email:** <**string**> (optional), the E-mail address of the receiver account.
* **params.phone:** <**string**> (optional), the Phone number of the receiver account.
* **params.address:** <**string**> (required), the Physical address of the receiver.

##### Examples

Default usage :

```php
<?php

use SlickPay\Transfer\Receiver;

$result = Receiver::update('3386ec0e-...-3abe0101d53f', [
    'title'   => "My new favorite receiver account",
    'rib'     => "12345678901234567890",
    'fname'   => "Jane",
    'lname'   => "Doe",
    'email'   => "jhon-doe@hotmail.com",
    'phone'   => "0770xxxxxx",
    'address' => "Jane Doe Address",
]);

dd($result);
```

##### Return value

The result will be an array like : 

* **success:** <**integer**>, 0 for false, 1 for true.
* **error:** <**integer**>, 0 for false, 1 for true.
* **messages:** <**array**>, contains error messages, it will be sent only when **error == 1**.
* **response:** <**array**>, the new user account related data attributes.

#### list

If you would like to get the user receiver accounts list, you will use the **list** method provided within the **Receiver** Class.

##### Parameters

* **offset:** <**number**> (optional), define wich offset will be used to paginate the resulted rows.

> **Important:** If no parameter is passed to the **list** method, all rows will be returned at once (no pagination made).

##### Example

Here is an example below :

```php
<?php

use SlickPay\Transfer\Receiver;

$result = Receiver::list(5);

dd($result);
```

##### Return value

The result will be an array like :

* **success:** <**integer**>, 0 for false, 1 for true
* **error:** <**integer**>, 0 for false, 1 for true
* **messages:** <**array**>, contains error messages, it will be sent only when **error == 1**
* **response:** <**array**>, it will be sent only when **success == 1**, it contains the API response
    * **data:** <**array**>, will contain all the payment rows.
    * **links:** <**array**>, pagination links that can be used to generate a pagination for the user UI.
    * **meta:** <**array**>, will contain all pagination data, such as: **total**, **current_page**, **per_page**...

> **Important:** The indexes **response.meta** and **response.links** will be present only when resulted rows are paginated (pass an integer for paymentHistory).

## More help
   * [Slick-Pay website](https://slick-pay.com)
   * [Reporting Issues / Feature Requests](https://github.com/Slick-Pay-Algeria/quick-transfer/issues)