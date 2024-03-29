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
   * The "webfonts" collection of methods.
   * Typical usage is:
   *  <code>
   *   $webfontsService = new apiWebfontsService(...);
   *   $webfonts = $webfontsService->webfonts;
   *  </code>
   */
  class WebfontsServiceResource extends apiServiceResource {


    /**
     * Retrieves the list of fonts currently served by the Google Web Fonts Developer API
     * (webfonts.list)
     *
     * @param array $optParams Optional parameters. Valid optional parameters are listed below.
     *
     * @opt_param string sort Enables sorting of the list
     * @return WebfontList
     */
    public function listWebfonts($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new WebfontList($data);
      } else {
        return $data;
      }
    }
  }



/**
 * Service definition for Webfonts (v1).
 *
 * <p>
 * The Google Web Fonts Developer API.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="http://code.google.com/apis/webfonts/docs/developer_api.html" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class apiWebfontsService extends apiService {
  public $webfonts;
  /**
   * Constructs the internal representation of the Webfonts service.
   *
   * @param apiClient apiClient
   */
  public function __construct(apiClient $apiClient) {
    $this->rpcPath = '/rpc';
    $this->restBasePath = '/webfonts/v1/';
    $this->version = 'v1';
    $this->serviceName = 'webfonts';
    $this->io = $apiClient->getIo();

    $apiClient->addService($this->serviceName, $this->version);
    $this->webfonts = new WebfontsServiceResource($this, $this->serviceName, 'webfonts', json_decode('{"methods": {"list": {"parameters": {"sort": {"enum": ["alpha", "date", "popularity", "style", "trending"], "type": "string", "location": "query"}}, "id": "webfonts.webfonts.list", "httpMethod": "GET", "path": "webfonts", "response": {"$ref": "WebfontList"}}}}', true));
  }
}

class Webfont extends apiModel {
  public $kind;
  public $variants;
  public $subsets;
  public $family;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setVariants($variants) {
    $this->variants = $variants;
  }
  public function getVariants() {
    return $this->variants;
  }
  public function setSubsets($subsets) {
    $this->subsets = $subsets;
  }
  public function getSubsets() {
    return $this->subsets;
  }
  public function setFamily($family) {
    $this->family = $family;
  }
  public function getFamily() {
    return $this->family;
  }
}

class WebfontList extends apiModel {
  protected $__itemsType = 'Webfont';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public function setItems(/* array(Webfont) */ $items) {
    $this->assertIsArray($items, 'Webfont', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
}
