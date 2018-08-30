<?php

namespace TicketShopBundle\Utils;

Class DevTestAPIClient {

    const DEFAULT_API_KEY = 'LzT2wxofRq3nlHryXUPejQ==';
    const API_URL = 'http://devtest.entradasatualcance.com/api/v1';

    private $auth_key = null;
    private $client = null;
    private $access_token = null;
    private $refresh_token = null;

    /**
     * DevTestAPIClient construct
     * 
     * @param string $api_key
     */
    public function __construct($api_key = null) {
        if (empty($api_key)) {
            //Sets default api key
            $this->auth_key = self::DEFAULT_API_KEY;
        } else {
            $this->auth_key = $api_key;
        }
        //Create a new GuzzleHttp Client
        $this->client = new \GuzzleHttp\Client(['base_uri' => self::API_URL]);
    }

    /**
     * Get access token and refresh token from the API
     * 
     * @return stdClass Object or  false on failure
     */
    public function getTokens() {
        try {
            $url = self::API_URL . '/oauth/token';
            //Sends the api_key 
            $data = ['api_key' => $this->getAuthKey()];
            $response = $this->client->post($url, array('form_params' => $data));
            $result = json_decode($response->getBody()->getContents());
            //Updates tokens
            $this->setAccessToken($result->access_token);
            $this->setRefreshToken($result->refresh_token);
            return $result;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get all events from the API
     * 
     * @return stdClass Object or  false on failure
     */
    public function getAllEvents() {
        return $this->getAPI(self::API_URL . '/events');
    }

    /**
     * Get a event by id from the API
     * 
     * @param int $id_event
     * @return stdClass Object or  false on failure
     */
    public function getEvent($id_event) {
        return $this->getAPI(self::API_URL . '/events/' . $id_event);
    }

    /**
     * Get all the tickets of an event by id from the API
     * 
     * @param int $id_event
     * @return stdClass Object or  false on failure
     */
    public function getAllEventTickets($id_event) {
        return $this->getAPI(self::API_URL . '/events/' . $id_event . '/tickets');
    }

    /**
     * Get all orders from the API
     * 
     * @return stdClass Object or  false on failure
     */
    public function getAllOrders() {
        return $this->getAPI(self::API_URL . '/orders');
    }

    /**
     * Create authorization header with access token 
     * 
     * @return string
     */
    private function getAuthorizationHeader() {
        if (empty($this->getAccessToken())) {
            $this->getTokens();
        }
        $header = array('Authorization' => 'Bearer ' . $this->getAccessToken());
        return $header;
    }

    /**
     * Make a request get to the api
     * 
     * @param string $url
     * @return stdClass Object or  false on failure
     */
    private function getAPI($url) {
        try {
            $response = $this->client->get($url, array('headers' => $this->getAuthorizationHeader()));
            $result = $response->getBody()->getContents();
            return json_decode($result);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Make a request post to the api
     * 
     * @param string $url
     * @param array $form 
     * @return stdClass Object or  false on failure
     */
    private function postAPI($url, $form) {
        try {
            $response = $this->client->post($url, array('headers' => $this->getAuthorizationHeader(), 'form_params' => $form));
            $result = $response->getBody()->getContents();
            return json_decode($result);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Place a Order to the API
     * 
     * @param string $name
     * @param string $lastname
     * @param string $documentid
     * @param string $zipcode
     * @param array $order
     * @return stdClass Object or  false on failure
     */
    public function placeOrder($name, $lastname, $documentid, $zipcode, $order) {

        $order_lines_type = array();
        foreach ($order as $ticket => $quantity) {
            if (!empty($quantity)) {
                array_push($order_lines_type, array('ticket' => $ticket, 'quantity' => $quantity));
            }
        }

        $order_type = array(
            'order[name]' => $name,
            'order[lastname]' => $lastname,
            'order[documentId]' => $documentid,
            'order[zipcode]' => $zipcode,
            'order[lines]' => $order_lines_type
        );
        return $this->postAPI(self::API_URL . '/orders', $order_type);
    }

    /**
     * Set Auth Key
     *
     * @param string $auth_key
     *
     * @return DevTestAPIClient
     */
    public function setAuthKey($auth_key) {
        $this->auth_key = $auth_key;

        return $this;
    }

    /**
     * Get auth_key
     *
     * @return string
     */
    public function getAuthKey() {
        return $this->auth_key;
    }

    /**
     * Set GuzzleClient
     *
     * @param \GuzzleHttp\Client $client
     *
     * @return DevTestAPIClient
     */
    public function setGuzzleClient($client) {
        $this->client = $client;

        return $this;
    }

    /**
     * Get GuzzleClient
     *
     * @return \GuzzleHttp\Client
     */
    public function getGuzzleClient() {
        return $this
                ->client;
    }

    /**
     * Set Access Token
     *
     * @param string $access_token
     *
     * @return DevTestAPIClient
     */
    public function setAccessToken($access_token) {
        $this->access_token = $access_token;

        return $this;
    }

    /**
     * Get access_token
     *
     * @return string
     */
    public function getAccessToken() {
        return $this->access_token;
    }

    /**
     * Set Refresh Token
     *
     * @param string $refresh_token
     *
     * @return DevTestAPIClient
     */
    public function setRefreshToken($refresh_token) {
        $this->refresh_token = $refresh_token;

        return $this;
    }

    /**
     * Get refresh_token
     *
     * @return string
     */
    public function getRefreshToken() {
        return $this->refresh_token;
    }

}
