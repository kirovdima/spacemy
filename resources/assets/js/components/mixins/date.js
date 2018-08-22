export const dateMixin = {
    data() {
        return {
            rus_months: [
                'январь',
                'февраль',
                'март',
                'апрель',
                'май',
                'июнь',
                'июль',
                'август',
                'сентябрь',
                'октябрь',
                'ноябрь',
                'декабрь',
            ],

            rus_months_genitive: [
                'января',
                'февраля',
                'марта',
                'апреля',
                'мая',
                'июня',
                'июля',
                'августа',
                'сентября',
                'октября',
                'ноября',
                'декабря',
            ],
        }
    },

    methods: {
        humanDate(date) {
            return date.getDate() + ' ' + this.rus_months_genitive[date.getMonth()]
        },

        humanWeek(date) {
            let end_week = new Date(this.start_date.getTime());
            end_week.setDate(end_week.getDate() + 7);

            return this.humanDate(this.start_date) + ' - ' + this.humanDate(end_week);
        },

        humanMonth(date) {
            return this.rus_months[date.getMonth()] + ' ' + date.getFullYear();
        }
    }
}
