export default class simupollCreatorDirective {
    constructor(SimupollCreatorController) {
        //class => use this
        this.restrict           = 'E'
        this.replace            = true
        this.controller         = SimupollCreatorController
        this.bindToController   = true//{ tree: '@', sid: '='} //to avoid using $scope
        this.controllerAs       = 'vm'
        this.templateUrl        = '../../../../../bundles/cpasimusantesimupoll/js/SimupollCreator/Partials/simupollCreator.directive.html'
    }
}
