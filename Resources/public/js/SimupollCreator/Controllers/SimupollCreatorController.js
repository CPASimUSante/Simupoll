export default class SimupollCreatorController {
    constructor() {

        // Initialize TinyMCE
        let tinymce               = window.tinymce
        tinymce.claroline.init    = tinymce.claroline.init || {}
        tinymce.claroline.plugins = tinymce.claroline.plugins || {}

        this.tinymceOptions = {}

        const plugins = [
            'autoresize advlist autolink lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars fullscreen',
            'insertdatetime media nonbreaking table directionality',
            'template paste textcolor emoticons code'
        ]
        let toolbar = 'undo redo | styleselect | bold italic underline | forecolor | alignleft aligncenter alignright | preview fullscreen'

        $.each(tinymce.claroline.plugins, function(key, value) {
            if ('autosave' != key &&  value === true) {
                plugins.push(key)
                toolbar += ' ' + key
            }
        })

        for (let prop in tinymce.claroline.configuration) {
            if (tinymce.claroline.configuration.hasOwnProperty(prop)) {
                this.tinymceOptions[prop] = tinymce.claroline.configuration[prop]
            }
        }

        this.tinymceOptions.plugins = plugins
        this.tinymceOptions.toolbar1 = toolbar
        this.tinymceOptions.trusted = true
        this.tinymceOptions.format = 'html'

        //questions / propositions
        this.dd = 'ABC';
        let simupollquestions = [
            {'title':'title1','id':1,'propositions':[{'choice':5,'mark':2},{'choice':3,'mark':1}]},
            {'title':'title2','id':2,'propositions':[{'choice':2,'mark':3}]},
            {'title':'title3','id':3,'propositions':[{'choice':1,'mark':4},{'choice':2,'mark':5},{'choice':3,'mark':6}]},
        ]
        this.simupollquestions = simupollquestions
    }

    addProposition(question) {

    }

    removeProposition(question) {

    }

    addQuestion(question) {

    }

    deleteQuestion(question) {

    }
}
