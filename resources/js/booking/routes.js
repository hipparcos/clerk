import auth from '../auth/routes.js'

import BookingIndexComponent from './table.vue'
import BookingNewComponent   from './new.vue'

const routes = [
    {
        path: '/bookings/new',
        name: 'bookings.new',
        component: BookingNewComponent,
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
]

export default {
    routes,
}
