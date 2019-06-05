import Vue from 'vue'
import axios from 'axios'
import VueAxios from 'vue-axios'
import { API_URL } from './config'
import Cookies from "js-cookie";

const ApiService = {
  init () {
    Vue.use(VueAxios, axios)
    Vue.axios.defaults.baseURL = API_URL
    Vue.axios.defaults.headers.common['Content-Language'] = Cookies.get('locale') || 'en'
  },

  query (resource, params) {
    return Vue.axios.get(resource, params).catch(error => {
      throw new Error(`[PORTFOLIO] ApiService ${error}`)
    })
  },

  get (resource, slug = '') {
    return Vue.axios.get(`${resource}/${slug}`).catch(error => {
      throw new Error(`[PORTFOLIO] ApiService ${error}`)
    })
  },

  post (resource, params) {
    return Vue.axios.post(`${resource}`, params)
  },

  update (resource, slug, params) {
    return Vue.axios.put(`${resource}/${slug}`, params)
  },

  put (resource, params) {
    return Vue.axios.put(`${resource}`, params)
  },

  delete (resource) {
    return Vue.axios.delete(resource).catch(error => {
      throw new Error(`[PORTFOLIO] ApiService ${error}`)
    })
  }
}

export default ApiService

export const ProjectsService = {
  query (params) {
    return ApiService.query('projects', {
      params: params
    })
  },
  get (slug) {
    return ApiService.get('projects', slug)
  }
}

export const CategoriesService = {
  query (params) {
    return ApiService.query('categories', {
      params: params
    })
  },
  get (slug) {
    return ApiService.get('categories', slug)
  }
}

export const EducationService = {
  query (params) {
    return ApiService.query('education', {
      params: params
    })
  },
  get (id) {
    return ApiService.get('education', id)
  }
}

export const ExperienceService = {
  query (params) {
    return ApiService.query('experience', {
      params: params
    })
  },
  get (id) {
    return ApiService.get('experience', id)
  }
}

export const SettingsService = {
  query (params) {
    return ApiService.query('settings', {
      params: params
    })
  },
  get (key) {
    return ApiService.get('settings', key)
  }
}

export const ContactService = {
  send(data) {
    return ApiService.post('contact/send', data);
  },
}
