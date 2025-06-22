
import { createRouter, createWebHistory } from 'vue-router';
import Bookpage from "@/pages/Bookpage.vue";
import Memberpage from '@/pages/Memberpage.vue';

const routes = [
    {
        path : "/book",
        name : "Book",
        component: Bookpage
    },
    {
        path : "/member",
        name : "Member",
        component: Memberpage
    },
]


const router = createRouter({
    'history': createWebHistory(),
    routes
})

export default router;