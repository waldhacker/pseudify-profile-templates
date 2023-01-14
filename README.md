[![Code quality](https://github.com/waldhacker/pseudify-profile-templates/actions/workflows/code-quality.yml/badge.svg)](https://github.com/waldhacker/pseudify-profile-templates/actions/workflows/code-quality.yml)

<h1>Work in progress</h1>
<h2>Don't try this at home.</h2>

---

**Pseudify** is a toolbox that helps you to pseudonymize database data.  
You can find hidden personal data in your database and you can pseudonymize them.  

&#127881; Analyze and pseudonymize supported databases from any application  
&#127881; Find hidden personal data  
&#127881; Data integrity: same input data generates same pseudonyms across all database columns  
&#127881; Analyze and pseudonymize easily encoded data  
&#127881; Analyze and pseudonymize multi-encoded data  
&#127881; Analyze and pseudonymize complex data structures like JSON or serialized PHP data  
&#127881; Analyze and pseudonymize dynamic data  
&#127881; 12 built-in decoders / encoders  
&#127881; Extensibility with custom decoders / encoders  
&#127881; 100+ built-in localizable fake data formats thanks to [FakerPHP](https://fakerphp.github.io/)  
&#127881; Extensibility with own fake data formats  
&#127881; Support for 7 built-in database platforms thanks to [Doctrine DBAL](https://www.doctrine-project.org/projects/dbal.html)  
&#127881; Extensibility with own database platforms  
&#127881; Modeling of profiles in PHP  

[See the documentation for more information](https://www.pseudify.me/docs/current/)

## Install

Download der ["Profile Templates"](https://github.com/waldhacker/pseudify-profile-templates):

```shell
docker run -it -v $(pwd):/app -u $(id -u):$(id -g) \
  composer create-project --no-dev --remove-vcs waldhacker/pseudify-profile-templates .
```

Start some database server

```shell
docker network create pseudify-net

docker run --rm --detach \
  --network pseudify-net \
  --name mariadb_10_5 \
  --env MARIADB_USER=pseudify \
  --env MARIADB_PASSWORD='pseudify(!)w4ldh4ck3r' \
  --env MARIADB_ROOT_PASSWORD='pseudify(!)w4ldh4ck3r' \
  --env MARIADB_DATABASE=pseudify_utf8mb4 \
  -v $(pwd)/tests/mariadb/10.5:/docker-entrypoint-initdb.d \
  mariadb:10.5
```

Configure pseudify

```
cp tests/mariadb/10.5/.env .env
```

Start pseudify

```
docker run -it -v $(pwd):/data --network=pseudify-net \
  ghcr.io/waldhacker/pseudify pseudify:debug:table_schema
```

[See the documentation for more information](https://www.pseudify.me/docs/current/setup/installation/)

## Quick guide for modeling pseudonymization

### I want to pseudonymize my users table

Well, that's easy:

```
<?php

namespace Waldhacker\Pseudify\Profiles;

use Waldhacker\Pseudify\Core\Processor\Processing\Pseudonymize\DataManipulatorPreset;
use Waldhacker\Pseudify\Core\Profile\Model\Pseudonymize\Column;
use Waldhacker\Pseudify\Core\Profile\Model\Pseudonymize\TableDefinition;
use Waldhacker\Pseudify\Core\Profile\Pseudonymize\ProfileInterface;

class MyPseudonymizeProfile implements ProfileInterface
{
    public function getIdentifier(): string
    {
        return 'my-custom-app';
    }

    public function getTableDefinition(): TableDefinition
    {
        $tableDefinition = new TableDefinition($this->getIdentifier());

        $tableDefinition
            ->addTable(table: 'users', columns: [
                // Replace the values in this column with fake data generated by FakerPHP/Faker formatter "userName"
                Column::create('username')->addDataProcessing(DataManipulatorPreset::scalarData('userName')),
                // Replace the values in this column with fake data generated by FakerPHP/Faker formatter "argon2iPassword"
                Column::create('password')->addDataProcessing(DataManipulatorPreset::scalarData('argon2iPassword')),
                // Replace the values in this column with fake data generated by FakerPHP/Faker formatter "safeEmail"
                Column::create('email')->addDataProcessing(DataManipulatorPreset::scalarData('safeEmail')),
            ])
        ;

        return $tableDefinition;
    }
}
```

### ... but the column with the first name is hexadecimal encoded (for some reason)

No problem:

```
<?php

namespace Waldhacker\Pseudify\Profiles;

use Waldhacker\Pseudify\Core\Processor\Processing\Pseudonymize\DataManipulatorPreset;
use Waldhacker\Pseudify\Core\Profile\Model\Pseudonymize\Column;
use Waldhacker\Pseudify\Core\Profile\Model\Pseudonymize\TableDefinition;
use Waldhacker\Pseudify\Core\Profile\Pseudonymize\ProfileInterface;

class MyPseudonymizeProfile implements ProfileInterface
{
    public function getIdentifier(): string
    {
        return 'my-custom-app';
    }

    public function getTableDefinition(): TableDefinition
    {
        $tableDefinition = new TableDefinition($this->getIdentifier());

        $tableDefinition
            ->addTable(table: 'users', columns: [
                // ...

                // -> Read the value from the database and decode the value in the column from hex to decimal
                // -> Replace the values in this column with fake data generated by FakerPHP/Faker formatter "firstName"
                // -> Encode the replaced value from decimal to hex and save the data back to the database
                Column::create('first_name', Column::DATA_TYPE_HEX)->addDataProcessing(DataManipulatorPreset::scalarData('firstName')),
            ])
        ;

        return $tableDefinition;
    }
}
```

### ... but the column with the last name is compressed with zlib and then stored in base64 encoded form in the database (for even more obscure reasons)

Well, we can deal with this:

```
<?php

namespace Waldhacker\Pseudify\Profiles;

use Waldhacker\Pseudify\Core\Processor\Encoder\Base64Encoder;
use Waldhacker\Pseudify\Core\Processor\Encoder\ChainedEncoder;
use Waldhacker\Pseudify\Core\Processor\Encoder\GzEncodeEncoder;
use Waldhacker\Pseudify\Core\Processor\Processing\Pseudonymize\DataManipulatorPreset;
use Waldhacker\Pseudify\Core\Profile\Model\Pseudonymize\Column;
use Waldhacker\Pseudify\Core\Profile\Model\Pseudonymize\TableDefinition;
use Waldhacker\Pseudify\Core\Profile\Pseudonymize\ProfileInterface;

class MyPseudonymizeProfile implements ProfileInterface
{
    public function getIdentifier(): string
    {
        return 'my-custom-app';
    }

    public function getTableDefinition(): TableDefinition
    {
        $tableDefinition = new TableDefinition($this->getIdentifier());

        $tableDefinition
            ->addTable(table: 'users', columns: [
                // ...

                // -> Read the value from the database and decode the value in the column from base64 to binary
                //    and then decompress the binary using zlib
                // -> Replace the values in this column with fake data generated by FakerPHP/Faker formatter "lastName"
                // -> Compress the replaced value using zlib and then encode the value as base64
                //    and save the data back to the database
                Column::create('last_name')
                    ->setEncoder(new ChainedEncoder([
                        new Base64Encoder(),
                        new GzEncodeEncoder([
                            GzEncodeEncoder::ENCODE_LEVEL => 5,
                            GzEncodeEncoder::ENCODE_ENCODING => ZLIB_ENCODING_GZIP,
                        ])
                    ]))
                    ->addDataProcessing(DataManipulatorPreset::scalarData('lastName')),
            ])
        ;

        return $tableDefinition;
    }
}
```

### ... but the content of the column "payload" depends on the value of the column "payload_type"

Why not, here we go:

```
<?php

namespace Waldhacker\Pseudify\Profiles;

use Waldhacker\Pseudify\Core\Processor\Processing\DataProcessing;
use Waldhacker\Pseudify\Core\Processor\Processing\Pseudonymize\DataManipulatorContext;
use Waldhacker\Pseudify\Core\Profile\Model\Pseudonymize\Column;
use Waldhacker\Pseudify\Core\Profile\Model\Pseudonymize\TableDefinition;
use Waldhacker\Pseudify\Core\Profile\Pseudonymize\ProfileInterface;

class MyPseudonymizeProfile implements ProfileInterface
{
    public function getIdentifier(): string
    {
        return 'my-custom-app';
    }

    public function getTableDefinition(): TableDefinition
    {
        $tableDefinition = new TableDefinition($this->getIdentifier());

        $tableDefinition
            ->addTable(table: 'users', columns: [
                // ...

                Column::create('payload')
                    ->addDataProcessing(new DataProcessing(function (DataManipulatorContext $context): void {
                          // The data of the column payload
                          $payload = $context->getProcessedData();
                          // The data of all columns
                          $databaseRow = $context->getDatebaseRow();

                          // Process the data in different ways depending
                          // on the content of the column payload_type
                          if ('foo' === $databaseRow['payload_type']) {
                              // replace payload with some fake username
                              $payload = $context->fake($payload)->userName();
                          } else {
                              // replace payload with some fake e-mail address
                              $payload = $context->fake($payload)->safeEmail();
                          }

                          // Replace the value in this column with the pseudonymized data
                          $context->setProcessedData($payload);
                      })),
            ])
        ;

        return $tableDefinition;
    }
}
```

### ... but the content of the column "session_data" contains complex data formats, in this case serialized PHP data

Nothing easier than that:

```
<?php

namespace Waldhacker\Pseudify\Profiles;

use Waldhacker\Pseudify\Core\Processor\Encoder\Serialized\Node\StringNode;
use Waldhacker\Pseudify\Core\Processor\Processing\DataProcessing;
use Waldhacker\Pseudify\Core\Processor\Processing\Pseudonymize\DataManipulatorContext;
use Waldhacker\Pseudify\Core\Profile\Model\Pseudonymize\Column;
use Waldhacker\Pseudify\Core\Profile\Model\Pseudonymize\TableDefinition;
use Waldhacker\Pseudify\Core\Profile\Pseudonymize\ProfileInterface;

class MyPseudonymizeProfile implements ProfileInterface
{
    public function getIdentifier(): string
    {
        return 'my-custom-app';
    }

    public function getTableDefinition(): TableDefinition
    {
        $tableDefinition = new TableDefinition($this->getIdentifier());

        $tableDefinition
            ->addTable(table: 'users', columns: [
                // ...

                // -> Read the value from the database and decode the
                //    serialized PHP value as an AST
                // -> Replace some values in the AST by hand with fake data
                // -> Transform the AST back into a serialized PHP value
                //    and save the data back to the database
                Column::create('session_data', Column::DATA_TYPE_SERIALIZED)
                    ->addDataProcessing(new DataProcessing(function (DataManipulatorContext $context): void {
                          // The plain data looks like this
                          // a:2:{i:0;s:14:"243.202.241.67";s:4:"user";O:8:"stdClass":5:{s:8:"userName";s:11:"georgiana59";s:8:"lastName";s:5:"Block";s:5:"email";s:19:"nolan11@example.net";s:2:"id";i:2;s:4:"user";R:3;}}

                          // $sessionDataNode contains the session data as an AST
                          $sessionDataNode = $context->getDecodedData();

                          // Collect the original data, which must be pseudonymized
                          $ip = $sessionDataNode->getPropertyContent(0)->getValue();
                          $userNode = $sessionDataNode->getPropertyContent('user');
                          $userName = $userNode->getPropertyContent('userName')->getValue();
                          $lastName = $userNode->getPropertyContent('lastName')->getValue();
                          $email = $userNode->getPropertyContent('email')->getValue();

                          // Collect the pseudonymized data
                          $fakeIp = $context->fake($ip)->ipv4();
                          $fakeUserName = $context->fake($userName)->userName();
                          $fakeLastName = $context->fake($lastName)->lastName();
                          $fakeEmail = $context->fake($email)->safeEmail();

                          // Pseudonymize the `$sessionDataNode` items
                          $sessionDataNode
                              ->replaceProperty(0, new StringNode($fakeIp))
                              ->getPropertyContent('user')
                                  ->replaceProperty('userName', new StringNode($fakeUserName))
                                  ->replaceProperty('lastName', new StringNode($fakeLastName))
                                  ->replaceProperty('email', new StringNode($fakeEmail))
                          ;

                          // Replace the value in this column with the pseudonymized data
                          $context->setProcessedData($sessionDataNode);
                      })),
            ])
        ;

        return $tableDefinition;
    }
}
```

### ... but the content of the column "session_data" contains complex data formats, in this case JSON-strings

Ok, do this:

```
<?php

namespace Waldhacker\Pseudify\Profiles;

use Waldhacker\Pseudify\Core\Processor\Processing\DataProcessing;
use Waldhacker\Pseudify\Core\Processor\Processing\Pseudonymize\DataManipulatorContext;
use Waldhacker\Pseudify\Core\Profile\Model\Pseudonymize\Column;
use Waldhacker\Pseudify\Core\Profile\Model\Pseudonymize\TableDefinition;
use Waldhacker\Pseudify\Core\Profile\Pseudonymize\ProfileInterface;

class MyPseudonymizeProfile implements ProfileInterface
{
    public function getIdentifier(): string
    {
        return 'my-custom-app';
    }

    public function getTableDefinition(): TableDefinition
    {
        $tableDefinition = new TableDefinition($this->getIdentifier());

        $tableDefinition
            ->addTable(table: 'users', columns: [
                // ...

                // -> Read the value from the database and decode it
                //    into an associative array
                // -> Replace some values in the array by hand with fake data
                // -> Transform the array back into a JSON-String
                //    and save the data back to the database
                Column::create('session_data', Column::DATA_TYPE_JSON)
                    ->addDataProcessing(new DataProcessing(function (DataManipulatorContext $context): void {
                          // The plain data looks like this
                          // {"userName":"ronaldo15","email":"mcclure.ofelia@example.com","lastName":"Keeling","ip":"1321:57fc:460b:d4d0:d83f:c200:4b:f1c8"}

                          // Get the decoded data (from json to array)
                          $sessionData = $context->getDecodedData();

                          // Collect the original data, which must be pseudonymized
                          $userName = $sessionData['userName'];
                          $email = $sessionData['email'];
                          $lastName = $sessionData['lastName'];
                          $ip = $sessionData['ip'];

                          // Collect the pseudonymized data
                          $fakeUserName = $context->fake($userName)->userName();
                          $fakeEmail = $context->fake($email)->safeEmail();
                          $fakeLastName = $context->fake($lastName)->lastName();
                          $fakeIp = $context->fake($ip)->ipv6();

                          // Pseudonymize the `$sessionData` items
                          $sessionData['userName'] = $fakeUserName;
                          $sessionData['email'] = $fakeEmail;
                          $sessionData['lastName'] = $fakeLastName;
                          $sessionData['ip'] = $fakeIp;

                          // Replace the value in this column with the pseudonymized data
                          $context->setProcessedData($sessionData);
                      })),
            ])
        ;

        return $tableDefinition;
    }
}
```
