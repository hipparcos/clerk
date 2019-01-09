import auth from '../auth/routes.js'

import BookingIndexComponent from './components/index.vue'
import BookingFormComponent   from './components/form.vue'

const routes = [
    {
        path: '/bookings/new',
        name: 'bookings.new',
        components: {
            default: BookingIndexComponent,
            top: BookingFormComponent,
        },
        beforeEnter: auth.ifAuthenticated,
        meta: {
            displayName: 'Book a room',
        },
    },
    {
        path: '/bookings/:id/edit',
        name: 'bookings.edit',
        components: {
            default: BookingIndexComponent,
            top: BookingFormComponent,
        },
        beforeEnter: auth.ifAuthenticated,
        props: { top: true },
        meta: {
            displayName: 'Edit a booking',
        },
    },
    {
        path: '/bookings',
        name: 'bookings.index',
        component: BookingIndexComponent,
        beforeEnter: auth.ifAuthenticated,
        meta: {
            displayName: 'View bookings',
        },
    },
    {
        path: '/bookings/:year/:month/:day',
        name: 'bookings.index.date',
        component: BookingIndexComponent,
        beforeEnter: auth.ifAuthenticated,
        props: true,
        meta: {
            displayName: 'View bookings',
        },
    },
    {
        path: '/bookings/agenda',
        name: 'bookings.index.agenda',
        component: BookingIndexComponent,
        beforeEnter: auth.ifAuthenticated,
        props: {
            agenda: true,
        },
        meta: {
            displayName: 'View bookings',
        },
    },
]

export default {
    routes,
}
