<?php
/**
 * Created by PhpStorm.
 * User: mbasr
 * Date: 27-Oct-16
 * Time: 2:55 AM
 */

require 'vendor/autoload.php';

use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class Request
{

    public $locations, $from, $to;

    private $_stack, $_client;

    public function __construct($location, $from, $to)
    {
        $this->locations = array_map('trim', explode(',', $location));
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Search and sort the results
     * @return array
     */
    public function search()
    {
        $users = $this->processRequest();
        if (count($users) > 0) {
            $sorted_users = $this->sortByProp($users, 'followers_count', true);
            return array_slice($sorted_users, 0, 50);
        }
        return $users;
    }

    /**
     * Sort results using prop
     * @param $array
     * @param $propName
     * @param bool $reverse
     * @return array
     */
    function sortByProp(array $array, $propName, $reverse = false)
    {
        $sorted = [];

        foreach ($array as $item)
        {
            $sorted[$item->$propName][] = $item;
        }

        if ($reverse) krsort($sorted); else ksort($sorted);
        $result = [];

        foreach ($sorted as $subArray) foreach ($subArray as $item)
        {
            $result[] = $item;
        }

        return $result;
    }

    /**
     * fetch results from twitter api
     * @return array
     */
    private function processRequest()
    {
        $client = $this->client;
        $locations = $this->locations;
        $from = $this->from;
        $to = $this->to;

        $requests = function ($total) use ($client, $locations) {
            $uri = 'users/search.json';

            foreach ($locations as $location) {
                for ($i = 0; $i < $total; $i++) {
                    $query = [
                        'q' => $location,
                        'page' => $i+1,
                        'include_entities' => true,
                    ];

                    yield function() use ($client, $uri, $query) {
                        return $client->getAsync($uri, ['query' => $query]);
                    };
                }
            }
        };

        $results = [];

        $pool = new Pool($client, $requests(30), [
            'concurrency' => 10,
            'fulfilled' => function ($response, $index) use (&$results, $from, $to, $locations) {
                $users = json_decode($response->getBody()->getContents());
                foreach ($users as $index => $user) {
                    if ($user->followers_count > $from && $user->followers_count < $to) {
                        if (count(array_intersect($locations, array_map('trim', explode(',', $user->location)))) > 0) {
                            $results[$user->id] = $user;
                        }
//                        array_push($results, $user);
                    }
                }
            },
            'rejected' => function ($reason, $index) {
                print_r($reason);
                exit;
            },
        ]);
        $promise = $pool->promise();
        $promise->wait();

        return $results;
    }

    public function getStack() {
        if (null === $this->_stack) {
            $stack = HandlerStack::create();

            $middleware = new Oauth1([
                'consumer_key'    => 'consumer_key',
                'consumer_secret' => 'consumer_secret',
                'token'           => 'token',
                'token_secret'    => 'token_secret'
            ]);
            $stack->push($middleware);
            $this->_stack = $stack;
        }
        return $this->_stack;
    }

    private function setStack($value) {
        $this->_stack = $value;
    }

    public function getClient() {
        if (null === $this->_client) {
            $client = new Client([
                'base_uri' => 'https://api.twitter.com/1.1/',
                'handler' => $this->stack,
                'auth' => 'oauth',
            ]);
            $this->_client = $client;
        }
        return $this->_client;
    }

    private function setClient($value) {
        $this->_client = $value;
    }

    public function __get( $name ) {
        if( method_exists( $this , $method = ( 'get' . ucfirst( $name  ) ) ) )
            return $this->$method();
        else
            throw new Exception( 'Can\'t get property ' . $name );
    }

    public function __set( $name , $value ) {
        if( method_exists( $this , $method = ( 'set' . ucfirst( $name  ) ) ) )
            return $this->$method( $value );
        else
            throw new Exception( 'Can\'t set property ' . $name );
    }

    public function __isset( $name )
    {
        return method_exists( $this , 'get' . ucfirst( $name  ) )
        || method_exists( $this , 'set' . ucfirst( $name  ) );
    }
}