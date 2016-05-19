<?php
require __DIR__ . '/../vendor/autoload.php';
use Aws\DynamoDb\DynamoDbClient;
use Aws\Credentials\CredentialProvider;
$client = DynamoDbClient::factory(array(
					'version' => 'latest',
					 'credentials' => CredentialProvider::ini('default', '/home/user/.aws/credentials'),
					 'region' => "us-west-2"
				));


// Create an "test" table for url lookup
$client->createTable(array(
    'TableName' => 'test',
    'AttributeDefinitions' => array(
        array(
            'AttributeName' => 'uuid',
            'AttributeType' => 'S'
        )
    ),
    'KeySchema' => array(
        array(
            'AttributeName' => 'uuid',
            'KeyType'       => 'HASH'
        )
    ),
    'ProvisionedThroughput' => array(
        'ReadCapacityUnits'  => 10,
        'WriteCapacityUnits' => 20
    )
));

$client->createTable(array(
    'TableName' => 'clicks',
    'AttributeDefinitions' => array(
        array(
            'AttributeName' => 'id',
            'AttributeType' => 'S'
        ),
        array(
            'AttributeName' => 'uuid',
            'AttributeType' => 'S'
        ),
    ),
    'KeySchema' => array(
        array(
            'AttributeName' => 'id',
            'KeyType'       => 'HASH'
        ),
        array(
            'AttributeName' => 'uuid',
            'KeyType'       => 'RANGE'
        ),
    ),
    'ProvisionedThroughput' => array(
        'ReadCapacityUnits'  => 10,
        'WriteCapacityUnits' => 20
    )
));
/*The table will now have a status of CREATING while the table is being provisioned. You can use a waiter to poll the table until it becomes ACTIVE.*/

// Wait until the table is created and active
$client->waitUntil('TableExists', array(
    'TableName' => 'test',
    'TableName' => 'clicks'
));
?>