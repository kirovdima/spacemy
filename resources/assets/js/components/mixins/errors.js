export const errorsMixin = {
    data () {
        return {
            errors: {
                402: {
                    'persons max count': 'К сожалению, в данной версии приложения можно следить не более чем за 5 своми друзьями',
                }
            }
        }
    },

    methods: {
        getTextError(code, type) {
            return this.errors[code][type];
        }
    }
}
