import auth from '../auth/routes.js'

import BookingIndexComponent from './table.vue'
import BookingNewComponent   from './new.vue'

const routes = [
    {
        path: '/bookings/new',
        name: 'bookings.new',
        components: {
            default: BookingIndexComponent,
            top: BookingNewComponent,
        },
        beforeEnter: auth.ifAuthenticated,
        meta: {
            displayName: 'Book a room',
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
]

export default {
    routes,
}
