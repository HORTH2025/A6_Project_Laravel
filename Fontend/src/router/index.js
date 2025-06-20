
import { createRouter, createWebHistory } from 'vue-router';
import Bookpage from "@/pages/Bookpage.vue";

const routes = [
    {
        path : "/book",
        name : "Book",
        component: Bookpage
    },
]


const router = createRouter({
    'history': createWebHistory(),
    routes
})

export default router;