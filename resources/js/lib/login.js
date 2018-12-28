import axios from 'axios'

// login executes a request to log in the user.
// @param String username
// @param String password
// @return axios instance
let login = function(username, password) {
    return axios({
        method: 'post',
        url: '/oauth/token',
        data: {
            grant_type: "password",
            client_id: 1,
            client_secret: "DvYKWsQPGXUPrRH41PsHtrMgtMMwfalJ0BjsoVhF",
            username: username,
            password: password,
            scope: "*"
        }
    })
}

export default {
    login: login
}
