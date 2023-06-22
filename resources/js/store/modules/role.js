import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_ROLES,
    UPDATE_ROLE,
    DELETE_ROLE,
    DELETE_ROLES,
    GET_ROLE,
    NEW_ROLE
} from "../../mutation_constants";

const state = {
    roles: [],
    role: null
};

const getters = {
    roles: state => state.roles,
    role: state => state.role
};

const actions = {
    // Add role
    async addRole({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/roles", data);

            commit(NEW_ROLE, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Role added successfully"
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

    // Get roles
    async getRoles({ commit }) {
        try {
            const res = await axios.get("/api/roles");

            commit(SET_LOADING, false, { root: true });
            commit(GET_ROLES, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single role
    async getRole({ commit }, roleId) {
        try {
            const res = await axios.get(`/api/roles/${roleId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_ROLE, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update role
    async updateRole({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/roles/${data.id}`, data);

            commit(UPDATE_ROLE, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Role updated successfully"
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

    // Delete role
    async deleteRole({ dispatch, commit }, roleId) {
        try {
            const res = await axios.delete(`/api/roles/${roleId}`);

            commit(DELETE_ROLE, roleId);

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

    // Delete multiple roles
    async deleteMultipleRoles({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/roles/delete_multiple`, {
                headers: {},
                data: { ids }
            });

            commit(DELETE_ROLES, ids);

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
    }
};

const mutations = {
    GET_ROLES: (state, payload) => (state.roles = payload),

    GET_ROLE: (state, payload) => (state.role = payload),

    NEW_ROLE: (state, payload) => {
        state.roles.unshift(payload);
    },

    UPDATE_ROLE: (state, payload) => {
        const index = state.roles.findIndex(role => role.id === payload.id);
        state.roles.splice(index, 1, payload);
    },

    DELETE_ROLE: (state, payload) => {
        const index = state.roles.findIndex(role => role.id === payload);
        state.roles.splice(index, 1);
    },

    DELETE_ROLES: (state, payload) => {
        payload.forEach(id => {
            const index = state.roles.findIndex(role => role.id === id);
            state.roles.splice(index, 1);
        });
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
