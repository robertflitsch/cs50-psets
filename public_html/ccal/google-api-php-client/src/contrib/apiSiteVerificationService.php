<?php
/*
 * Copyright (c) 2010 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

require_once 'service/apiModel.php';
require_once 'service/apiService.php';
require_once 'service/apiServiceRequest.php';


  /**
   * The "webResource" collection of methods.
   * Typical usage is:
   *  <code>
   *   $siteVerificationService = new apiSiteVerificationService(...);
   *   $webResource = $siteVerificationService->webResource;
   *  </code>
   */
  class WebResourceServiceResource extends apiServiceResource {


    /**
     * Attempt verification of a website or domain. (webResource.insert)
     *
     * @param string $verificationMethod The method to use for verifying a site or domain.
     * @param SiteverificationWebResourceResource $postBody
     * @return SiteverificationWebResourceResource
     */
    public function insert($verificationMethod, SiteverificationWebResourceResource $postBody) {
      $params = array('verificationMethod' => $verificationMethod, 'postBody' => $postBody);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new SiteverificationWebResourceResource($data);
      } else {
        return $data;
      }
    }
    /**
     * Get the most current data for a website or domain. (webResource.get)
     *
     * @param string $id The id of a verified site or domain.
     * @return SiteverificationWebResourceResource
     */
    public function get($id) {
      $params = array('id' => $id);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new SiteverificationWebResourceResource($data);
      } else {
        return $data;
      }
    }
    /**
     * Get the list of your verified websites and domains. (webResource.list)
     *
     * @return SiteverificationWebResourceListResponse
     */
    public function listWebResource() {
      $params = array();
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new SiteverificationWebResourceListResponse($data);
      } else {
        return $data;
      }
    }
    /**
     * Modify the list of owners for your website or domain. (webResource.update)
     *
     * @param string $id The id of a verified site or domain.
     * @param SiteverificationWebResourceResource $postBody
     * @return SiteverificationWebResourceResource
     */
    public function update($id, SiteverificationWebResourceResource $postBody) {
      $params = array('id' => $id, 'postBody' => $postBody);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new SiteverificationWebResourceResource($data);
      } else {
        return $data;
      }
    }
    /**
     * Modify the list of owners for your website or domain. This method supports patch semantics.
     * (webResource.patch)
     *
     * @param string $id The id of a verified site or domain.
     * @param SiteverificationWebResourceResource $postBody
     * @return SiteverificationWebResourceResource
     */
    public function patch($id, SiteverificationWebResourceResource $postBody) {
      $params = array('id' => $id, 'postBody' => $postBody);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new SiteverificationWebResourceResource($data);
      } else {
        return $data;
      }
    }
    /**
     * Get a verification token for placing on a website or domain. (webResource.getToken)
     *
     * @param array $optParams Optional parameters. Valid optional parameters are listed below.
     *
     * @opt_param string verificationMethod The method to use for verifying a site or domain.
     * @opt_param string identifier The URL or domain to verify.
     * @opt_param string type Type of resource to verify. Can be 'site' (URL) or 'inet_domain' (domain name).
     * @return SiteverificationWebResourceGettokenResponse
     */
    public function getToken($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('getToken', array($params));
      if ($this->useObjects()) {
        return new SiteverificationWebResourceGettokenResponse($data);
      } else {
        return $data;
      }
    }
    /**
     * Relinquish ownership of a website or domain. (webResource.delete)
     *
     * @param string $id The id of a verified site or domain.
     */
    public function delete($id) {
      $params = array('id' => $id);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }



/**
 * Service definition for SiteVerification (v1).
 *
 * <p>
 * Lets you programatically verify ownership of websites or domains with Google.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="http://code.google.com/apis/siteverification/" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class apiSiteVerificationService extends apiService {
  public $webResource;
  /**
   * Constructs the internal representation of the SiteVerification service.
   *
   * @param apiClient apiClient
   */
  public function __construct(apiClient $apiClient) {
    $this->rpcPath = '/rpc';
    $this->restBasePath = '/siteVerification/v1/';
    $this->version = 'v1';
    $this->serviceName = 'siteVerification';
    $this->io = $apiClient->getIo();

    $apiClient->addService($this->serviceName, $this->version);
    $this->webResource = new WebResourceServiceResource($this, $this->serviceName, 'webResource', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/siteverification"], "parameters": {"verificationMethod": {"required": true, "type": "string", "location": "query"}}, "request": {"$ref": "SiteverificationWebResourceResource"}, "id": "siteVerification.webResource.insert", "httpMethod": "POST", "path": "webResource", "response": {"$ref": "SiteverificationWebResourceResource"}}, "get": {"scopes": ["https://www.googleapis.com/auth/siteverification"], "parameters": {"id": {"required": true, "type": "string", "location": "path"}}, "id": "siteVerification.webResource.get", "httpMethod": "GET", "path": "webResource/{id}", "response": {"$ref": "SiteverificationWebResourceResource"}}, "list": {"scopes": ["https://www.googleapis.com/auth/siteverification"], "id": "siteVerification.webResource.list", "httpMethod": "GET", "path": "webResource", "response": {"$ref": "SiteverificationWebResourceListResponse"}}, "update": {"scopes": ["https://www.googleapis.com/auth/siteverification"], "parameters": {"id": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "SiteverificationWebResourceResource"}, "id": "siteVerification.webResource.update", "httpMethod": "PUT", "path": "webResource/{id}", "response": {"$ref": "SiteverificationWebResourceResource"}}, "patch": {"scopes": ["https://www.googleapis.com/auth/siteverification"], "parameters": {"id": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "SiteverificationWebResourceResource"}, "id": "siteVerification.webResource.patch", "httpMethod": "PATCH", "path": "webResource/{id}", "response": {"$ref": "SiteverificationWebResourceResource"}}, "getToken": {"scopes": ["https://www.googleapis.com/auth/siteverification"], "parameters": {"type": {"type": "string", "location": "query"}, "identifier": {"type": "string", "location": "query"}, "verificationMethod": {"type": "string", "location": "query"}}, "response": {"$ref": "SiteverificationWebResourceGettokenResponse"}, "httpMethod": "GET", "path": "token", "id": "siteVerification.webResource.getToken"}, "delete": {"scopes": ["https://www.googleapis.com/auth/siteverification"], "parameters": {"id": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE", "path": "webResource/{id}", "id": "siteVerification.webResource.delete"}}}', true));
  }
}

class SiteverificationWebResourceGettokenRequest extends apiModel {
  public $verificationMethod;
  protected $__siteType = 'SiteverificationWebResourceGettokenRequestSite';
  protected $__siteDataType = '';
  public $site;
  public function setVerificationMethod($verificationMethod) {
    $this->verificationMethod = $verificationMethod;
  }
  public function getVerificationMethod() {
    return $this->verificationMethod;
  }
  public function setSite(SiteverificationWebResourceGettokenRequestSite $site) {
    $this->site = $site;
  }
  public function getSite() {
    return $this->site;
  }
}

class SiteverificationWebResourceGettokenRequestSite extends apiModel {
  public $identifier;
  public $type;
  public function setIdentifier($identifier) {
    $this->identifier = $identifier;
  }
  public function getIdentifier() {
    return $this->identifier;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class SiteverificationWebResourceGettokenResponse extends apiModel {
  public $token;
  public $method;
  public function setToken($token) {
    $this->token = $token;
  }
  public function getToken() {
    return $this->token;
  }
  public function setMethod($method) {
    $this->method = $method;
  }
  public function getMethod() {
    return $this->method;
  }
}

class SiteverificationWebResourceListResponse extends apiModel {
  protected $__itemsType = 'SiteverificationWebResourceResource';
  protected $__itemsDataType = 'array';
  public $items;
  public function setItems(/* array(SiteverificationWebResourceResource) */ $items) {
    $this->assertIsArray($items, 'SiteverificationWebResourceResource', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
}

class SiteverificationWebResourceResource extends apiModel {
  public $owners;
  public $id;
  protected $__siteType = 'SiteverificationWebResourceResourceSite';
  protected $__siteDataType = '';
  public $site;
  public function setOwners(/* array(string) */ $owners) {
    $this->assertIsArray($owners, 'string', __METHOD__);
    $this->owners = $owners;
  }
  public function getOwners() {
    return $this->owners;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setSite(SiteverificationWebResourceResourceSite $site) {
    $this->site = $site;
  }
  public function getSite() {
    return $this->site;
  }
}

class SiteverificationWebResourceResourceSite extends apiModel {
  public $identifier;
  public $type;
  public function setIdentifier($identifier) {
    $this->identifier = $identifier;
  }
  public function getIdentifier() {
    return $this->identifier;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}
