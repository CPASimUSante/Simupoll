/**
 * CommonApp
 */

import angular from 'angular/index'
//import classes called in dependency injection
//for $uibModal
import {} from 'angular-bootstrap'

import commonService from './Services/CommonService'
import commonController from './Controllers/CommonController'

// Category module
angular
    .module('CategoryApp', [
        'ui.bootstrap'
    ])
    .service('CommonService', [
        commonService
    ])
    //modal management
    .factory('modalFactory', [
        '$uibModal',
        $modal => ({
            open: template => $modal.open({ template })
        })
      ])
    .controller('CommonController', [
        'CommonService',
        'modalFactory',
        commonController
    ])
