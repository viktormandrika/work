import Field from '../models/Field';
import {VTextField} from 'vuetify/lib'
import DatePicker from '../components/DatePicker'

export default {
  first_name: Object.assign({}, Field, {
    name: 'fist_name',
    label: 'Имя',
    rules: [
      v => (v && v.length >= 3) || 'Больше 3 символов'
    ],
    component: VTextField
  }),
  second_name: Object.assign({}, Field, {
    name: 'second_name',
    label: 'Фамилия',
    rules: [
      v => (v && v.length >= 3) || 'Больше 3 символов'
    ],
    component: VTextField
  }),
  date: Object.assign({}, Field, {
    rules: [],
    component: DatePicker
  }),
  email: Object.assign({}, Field, {
    name: 'email',
    label: 'Email*',
    rules: [v => /.+@.+/.test(v) || 'Email должен быть правильным',],
    component: VTextField
  }),
  phone: Object.assign({}, Field, {
    name: 'phone',
    label: 'Номер телефона',
    rules: [
      v => (v && v.length >= 11) || 'Введите 11 символов',
      v => !!v || 'Номер телефона обязателен к заполнению'
    ],
    component: VTextField,
    maskPhone: '+## (###) ## - ## - ###'
  }),
}