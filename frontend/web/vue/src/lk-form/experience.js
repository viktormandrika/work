import Field from '../models/Field';
import {VTextField, VSelect, VTextarea} from 'vuetify/lib'

export default {
  name: Object.assign({}, Field, {
    name: 'name',
    label: 'Название компании',
    component: VTextField,
    rules: []
  }),
  post: Object.assign({}, Field, {
    name: 'post',
    label: 'Должность',
    component: VTextField,
    rules: []
  }),
  month_from: Object.assign({}, Field, {
    name: 'month_from',
    label: 'Месяц начала',
    rules: [],
    component: VSelect,
    items: [
      {
        name: 'Январь',
        id: 1
      },
      {
        name: 'Февраль',
        id: 2
      },
      {
        name: 'Март',
        id: 3
      },
      {
        name: 'Апрель',
        id: 4
      },
      {
        name: 'Май',
        id: 5
      },
      {
        name: 'Июнь',
        id: 6
      },
      {
        name: 'Июль',
        id: 7
      },
      {
        name: 'Август',
        id: 8
      },
      {
        name: 'Сентябрь',
        id: 9
      },
      {
        name: 'Октябрь',
        id: 10
      },
      {
        name: 'Ноябрь',
        id: 11
      },
      {
        name: 'Декабрь',
        id: 12
      }
    ]
  }),
  year_from: Object.assign({}, Field, {
    name: 'year_from',
    label: 'Год начала',
    component: VTextField,
    type: 'number',
    rules: [v => (v === '') || (/^\d+[\.,]{0,1}\d+$/.test(v) || 'Только цифры')]
  }),
  month_to: Object.assign({}, Field, {
    name: 'month_to',
    label: 'Месяц окончания',
    rules: [],
    component: VSelect,
    items: [
      {
        name: 'Январь',
        id: 1
      },
      {
        name: 'Февраль',
        id: 2
      },
      {
        name: 'Март',
        id: 3
      },
      {
        name: 'Апрель',
        id: 4
      },
      {
        name: 'Май',
        id: 5
      },
      {
        name: 'Июнь',
        id: 6
      },
      {
        name: 'Июль',
        id: 7
      },
      {
        name: 'Август',
        id: 8
      },
      {
        name: 'Сентябрь',
        id: 9
      },
      {
        name: 'Октябрь',
        id: 10
      },
      {
        name: 'Ноябрь',
        id: 11
      },
      {
        name: 'Декабрь',
        id: 12
      }
    ]
  }),
  year_to: Object.assign({}, Field, {
    name: 'year_to',
    label: 'Год окончания',
    component: VTextField,
    type: 'number',
    rules: [v => (v === '') || (/^\d+[\.,]{0,1}\d+$/.test(v) || 'Только цифры')]
  }),
  responsibility: Object.assign({}, Field, {
    name: 'responsibility',
    label: 'Описание',
    component: VTextarea,
    counter: 2000,
    rules: []
  })
}