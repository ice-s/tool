import Vue from "vue";
import moment from 'moment';

Vue.filter('formatDateTime', function(value) {
    if (value) {
        return moment(String(value)).format('DD/MM/YYYY hh:mm:ss')
    }
});

Vue.filter('formatDate', function(value) {
    if (value) {
        return moment(String(value)).format('DD/MM/YYYY')
    }
});
