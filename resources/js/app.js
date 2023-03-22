/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
// import { createApp } from 'vue';

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

// const app = createApp({});

// import ExampleComponent from './components/ExampleComponent.vue';
// app.component('example-component', ExampleComponent);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

// app.mount('#app');

// SUMMERNOTE LOADING FUNCTION

// document.addEventListener("DOMContentLoaded", () => {
//   if (document.querySelector('[name=outfit_about]')) {
//     $('[name=outfit_about]').summernote();
//   }
// });

$(document).ready(function () {
  $('[name=outfit_about]').summernote();
});

// Masters JS rendering
document.addEventListener("DOMContentLoaded", () => {
  if (document.querySelector('#root')) {
    let hash;
    if (!location.hash) {
      hash = '#list';
    }
    else {
      hash = location.hash;
    }
    const url = apiUrl + '/list';
    request(url);
  }
});

window.addEventListener("hashchange", () => {
  getHash();
});

const getHash = () => {
  const url = apiUrl + '/' + location.hash.substr(1).replace('|', '/');
  request(url);
}

const request = (url) => {
  const root = document.querySelector('#root');
  axios.get(url, {})
    .then(function (response) {
      root.innerHTML = response.data.html;
      hydrationPagination(root);
    })
    .catch(function (error) {
      console.log(error);
    });
}

const postRequest = (url, data) => {
  axios.post(url, data)
    .then(function (response) {
      if (response.data.hash === undefined) {
        response.data.hash = location.hash.substr(1);
      }
      if (location.hash.substr(1) == response.data.hash) {
        getHash();
      }
      else {
        window.location.hash = response.data.hash;
      }
      if (response.data.msg) {
        document.querySelector('#msg').innerHTML = response.data.msg;
      }
      else {
        document.querySelector('#msg').innerHTML = '';
      }
    })
    .catch(function (error) {
      console.log(error);
    });
}

const hydrationPagination = node => {
  node.querySelectorAll('a.link-btn').forEach(a => {
    a.addEventListener('click', () => {
      document.querySelector('#msg').innerHTML = '';
    })
  })
  node.querySelectorAll('a.page-link').forEach(a => {
    a.addEventListener('click', e => {
      document.querySelector('#msg').innerHTML = '';
      e.preventDefault();
      const url = e.target.getAttribute('href');
      request(url);
    })
  })
  node.querySelectorAll('button.btn').forEach(b => {
    b.addEventListener('click', (e) => {
      document.querySelector('#msg').innerHTML = '';
      e.preventDefault();
      const form = b.closest('form');
      const url = form.getAttribute('action');
      const data = {};
      form.querySelectorAll('[name]').forEach(i => {
        data[i.getAttribute('name')] = i.value;
      })
      postRequest(url, data);
    })
  })
}