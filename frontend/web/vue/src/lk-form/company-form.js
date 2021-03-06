import Field from '../models/Field';
import {VTextarea, VTextField, VCheckbox} from 'vuetify/lib'
import AddSocial from "../components/AddSocial";

export default {
  privatePerson: Object.assign({}, Field, {
    name: 'privatePerson',
    label: 'Частное лицо',
    rules: [],
    class: 'privatePerson',
    component: VCheckbox
  }),
  nameCompany: Object.assign({}, Field, {
    name: 'nameCompany',
    label: 'Название компании*',
    rules: [v => !!v || 'Название компании обязательно к заполнению'],
    counter: 50,
    id: 'nameCompany',
    class: 'jsCompanyInput',
    component: VTextField
  }),
  site: Object.assign({}, Field, {
    name: 'site',
    label: 'Сайт',
    rules: [],
    component: VTextField,
    class: 'jsCompanyInput',
    id: 'site'
  }),
  scopeOfTheCompany: Object.assign({}, Field, {
    name: 'scopeOfTheCompany',
    label: 'Сфера деятельности компании*',
    rules: [v => !!v || 'Сфера деятельности компании обязателена к заполнению'],
    component: VTextarea,
    counter: 2000,
    class: 'jsCompanyInput',
    id: 'scopeOfTheCompany'
  }),
  addSocial: Object.assign({}, Field, {
    component: AddSocial,
    rules: [],
  }),
  aboutCompany: Object.assign({}, Field, {
    name: 'aboutCompany',
    label: 'О компании',
    rules: [],
    component: VTextarea,
    counter: 2000,
    class: 'jsCompanyInput',
    id: 'aboutCompany'
  }),
  contactPerson: Object.assign({}, Field, {
    name: 'contactPerson',
    label: 'Контактное лицо*',
    rules: [v => !!v || 'Контактное лицо обязателено к заполнению'],
    counter: 50,
    component: VTextField
  }),
  companyPhone: Object.assign({}, Field, {
    name: 'companyPhone',
    label: 'Телефон',
    rules: [v => (v === '') || (/^\d+[\.,]{0,1}\d+$/.test(v) || 'Только цифры')],
    component: VTextField,
    // maskPhone: '+## (###) ## - ## - ###'
  }),
}