import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_USERS,
    UPDATE_USER,
    DELETE_USER,
    DELETE_USERS,
    GET_USER,
    NEW_USER
} from "../../mutation_constants";

const state = {
    users: [],
    user: null
};

const getters = {
    users: state => state.users,
    user: state => state.user
};

const actions = {
    // Add user
    async addUser({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/users", data);

            commit(NEW_USER, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "User added successfully"
                },
                { root: true }
            );
        } catch (error) {
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true
                });
            }
        }
    },

    // Get users
    async getUsers({ commit }) {
        try {
            const res = await axios.get("/api/users");

            commit(GET_USERS, res.data);
            commit(SET_LOADING, false, { root: true });
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single user
    async getUser({ commit }, userId) {
        try {
            const res = await axios.get(`/api/users/${userId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_USER, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update user
    async updateUser({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/users/${data.id}`, data);

            commit(UPDATE_USER, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "User updated successfully"
                },
                { root: true }
            );
        } catch (error) {
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true
                });
            }
            console.log(error);
        }
    },

    // Delete user
    async deleteUser({ dispatch, commit }, userId) {
        try {
            const res = await axios.delete(`/api/users/${userId}`);

            commit(DELETE_USER, userId);

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: res.data.success
                },
                { root: true }
            );
        } catch (error) {
            console.log(error);
        }
    },

    // Delete multiple users
    async deleteMultipleUsers({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/users/delete_multiple`, {
                headers: {},
                data: { ids }
            });

            commit(DELETE_USERS, ids);

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: res.data.success
                },
                { root: true }
            );
        } catch (error) {
            console.log(error);
        }
    },

    // Edit user account
    async editUserAccount({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/users/edit_user_account`, data);

            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: res.data.success
                },
                { root: true }
            );
        } catch (error) {
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true
                });
            }
            console.log(error);
        }
    }
};

const mutations = {
    GET_USERS: (state, payload) => (state.users = payload),

    GET_USER: (state, payload) => (state.user = payload),

    NEW_USER: (state, payload) => {
        state.users.push(payload);
    },

    UPDATE_USER: (state, payload) => {
        const index = state.users.findIndex(user => user.id === payload.id);
        state.users.splice(index, 1, payload);
    },

    DELETE_USER: (state, payload) => {
        const index = state.users.findIndex(user => user.id === payload);
        state.users.splice(index, 1);
    },

    DELETE_USERS: (state, payload) => {
        payload.forEach(id => {
            const index = state.users.findIndex(user => user.id === id);
            state.users.splice(index, 1);
        });
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
