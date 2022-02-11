require('./bootstrap');

import { createApp } from 'vue';

import ColumnsList from './components/ColumnsList.vue';
import { vfmPlugin } from 'vue-final-modal'

const app = createApp({});
app.component(
    'columns-list',
    ColumnsList
)

app.use(vfmPlugin)
app.mount('#app');
