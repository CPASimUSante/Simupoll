export default class simupollCreatorDirective {
    constructor(SimupollCreatorController, simupollCreatorTemplate) {
        //class => use this
        this.restrict           = 'E'
        this.replace            = true
        this.controller         = SimupollCreatorController
        this.bindToController   = true//{ tree: '@', sid: '='} //to avoid using $scope
        this.controllerAs       = 'vm'
        this.template           = simupollCreatorTemplate
    }
}
