import { createRouter, createWebHistory } from 'vue-router'
import Bookpage from '../pages/Bookpage.vue'
import Author from '../pages/Author.vue'
import Memberpage from '../pages/Memberpage.vue'

const routes = [
  { path: '/book', component: Bookpage },
  { path: '/author', component: Author },
  { path: '/member', component: Memberpage },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
