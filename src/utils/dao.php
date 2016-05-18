<?php
/**
 * This package requires that the aws sdk for php be installed.
 * See: https://aws.amazon.com/sdkforphp/ for more information,
 * Or use the included composer config to install it automatically.
 */

//namespace utils\url_shortening;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Enum\ComparisonOperator;
use Aws\DynamoDb\Enum\Type;
use Aws\Common\Enum\Region;
use Aws\DynamoDb\Marshaler;


/**
 * Class for storing table names as constants; makes things a bit easier if table names change.
 * Accessed through db::TABLE_NAME
 */
abstract class Entities
{
	const URL_TABLE = 'reverse_url_mapper';

	protected static $client=NULL;
	public static $marshaler = NULL;

	public function __construct(){
		try{
			if(!self::$client){
				self::$client = DynamoDbClient::factory(array(
					'version' => 'latest',
					'credentials' => [
       					 'key' => '',
						'secret' => '',
    				],
					'region' => "us-west-2"
				));

				self::$marshaler = new Marshaler();
				//print_r($temp);
			}


		}catch(Exception $e){
			return $this->error($e,"connection failed");
		}

	}

	/**
	 * Basic exception handling function.
	 * Returns the amazon api exception message, the prepared query data, and the stack trace of the exception.
	 * @param  object $exception The object of the exception thrown
	 * @param  array  $queryData The prepared query data
	 */
	protected function error($exception, $queryData = null) {
		print 'Dynamo error: ' . $exception->getMessage();
		print "\n\nQuery data: ";
		print_r($queryData);
		print "\nStack trace:";
		$trace = $exception->getTrace();
		foreach($trace as $key => $value) {
			print "\nFunction " . $value['function'] . ' in ' . $value['file'] . ':' . $value['line'];
		}
	//	die;
	}
}

class Dao extends Entities{

    const STRING_TYPE = 'S';//string
    const NUMBER_TYPE = 'N';//number
    const BINARY_TYPE = 'B';//binary
    const STRING_ARRAY_TYPE = 'SS';//array of strings
    const NUMBER_ARRAY_TYPE = 'NS';//array of numbers
    const BINARY_ARRAY_TYPE = 'BS';//array of binary

    const EQUALS = 'EQ';//equals
    const NOT_EQUALS = 'NE';//not equals
    const IN = 'IN';//exact match
    const LESS_EQUAL = 'LE';//less or equal to
    const LESS_THAN = 'LT';//less than
    const GREATER_EQUAL = 'GE';//greater or equal to
    const GREATER_THAN = 'GT';//greater than
    const BETWEEN = 'BETWEEN';//between
    const NOT_NULL ='NOT_NULL';//not null
    const IS_NULL = 'NULL';//null
    const CONTAINS = 'CONTAINS';//contains
    const NOT_CONTAINS = 'NOT_CONTAINS';//doesn't contain
    const BEGINS_WITH = 'BEGINS_WITH';//begins with

    private $_voTableName;
    protected $__tableInfo;

	####################################################
    ## Pre: $table = table name as is in DB
    ##      $primary = primary key this table is indexed by
    ##      $voObj = blank instance of the class this DAO works with.
    ####################################################
    public function __construct($table) {
		try{
			parent::__construct();
			$this->__tableInfo = self::$client->describeTable(array('TableName' => $table));
			$this->_voTableName = $table;
		}catch(Exception $e){
			return $this->error($e,"invalid tablename $table");
		}
    }


	public function put($object){
		try{
			$result = self::$client->putItem(array(
				'TableName' => $this->_voTableName,
				'Item' => self::$marshaler->marshalItem($object),
				'ReturnConsumedCapacity' => 'TOTAL'
			));
			return $result;
		}catch(Exception $e){
			@error_log($this->error($e,"invalid tablename $this->_voTableName"));
			return NULL;
		}
	}

	public function get($value, $keyIndex = 'uuid'){
		$queryData = array(
			'ConsistentRead' => true,
			'TableName' => $this->_voTableName,
			'Key' => array(
				$keyIndex => array('S'=> $value)
			)
		);
		//print_r($queryData);
		try{
			$response = self::$client->getItem($queryData);


			$formattedResponse = array();
			if(!empty($response['Item'])) {

				foreach($response['Item'] as $key => $value) {
					$entity_value=array_pop($value);
					$formattedResponse[$key] =   $entity_value;
				}
			} else {
				return array();
			}
		}catch(Exception $e){
			return NULL;
			@error_log($this->error($e,"invalid get $value $keyIndex"));
		}

		return $formattedResponse;
	}

	public function scan($filters){
		$iterator = self::$client->getIterator('Scan', array(
		    'TableName' => $this->_voTableName,
		    'ScanFilter' => $filters
		));

		return $iterator;
	}

	public function query($filters, $index=null){

		$iteratorArray = array(
			'TableName' => $this->_voTableName,
			'KeyConditions' => $filters
		);

		if($index){
			$iteratorArray['IndexName'] = $index;
		}

		$iterator = self::$client->getIterator('Query', $iteratorArray);

		return $iterator;
	}

	/**
     * Instantiates a new DAO.
     *
     * @param string $className Entity class name
     * @return DAO An instantiated Data Access Object
     */
    public static function Instantiate($className) {
        return new Dao($className);
    }

}