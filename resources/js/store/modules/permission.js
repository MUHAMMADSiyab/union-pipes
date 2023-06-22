import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_PERMISSIONS,
    UPDATE_PERMISSION,
    DELETE_PERMISSION,
    DELETE_PERMISSIONS,
    GET_PERMISSION,
    NEW_PERMISSION
} from "../../mutation_constants";

const state = {
    permissions: [],
    permission: null
};

const getters = {
    permissions: state => state.permissions,
    permission: state => state.permission
};

const actions = {
    // Add permission
    async addPermission({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/permissions", data);

            commit(NEW_PERMISSION, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Permission added successfully"
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

    // Get permissions
    async getPermissions({ commit }) {
        try {
            const res = await axios.get("/api/permissions");

            commit(SET_LOADING, false, { root: true });
            commit(GET_PERMISSIONS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single permission
    async getPermission({ commit }, permissionId) {
        try {
            const res = await axios.get(`/api/permissions/${permissionId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_PERMISSION, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update permission
    async updatePermission({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/permissions/${data.id}`, data);

            commit(UPDATE_PERMISSION, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Permission updated successfully"
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

    // Delete permission
    async deletePermission({ dispatch, commit }, permissionId) {
        try {
            const res = await axios.delete(`/api/permissions/${permissionId}`);

            commit(DELETE_PERMISSION, permissionId);

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

    // Delete multiple permissions
    async deleteMultiplePermissions({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/permissions/delete_multiple`, {
                headers: {},
                data: { ids }
            });

            commit(DELETE_PERMISSIONS, ids);

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
    GET_PERMISSIONS: (state, payload) => (state.permissions = payload),

    GET_PERMISSION: (state, payload) => (state.permission = payload),

    NEW_PERMISSION: (state, payload) => {
        state.permissions.unshift(payload);
    },

    UPDATE_PERMISSION: (state, payload) => {
        const index = state.permissions.findIndex(
            permission => permission.id === payload.id
        );
        state.permissions.splice(index, 1, payload);
    },

    DELETE_PERMISSION: (state, payload) => {
        const index = state.permissions.findIndex(
            permission => permission.id === payload
        );
        state.permissions.splice(index, 1);
    },

    DELETE_PERMISSIONS: (state, payload) => {
        payload.forEach(id => {
            const index = state.permissions.findIndex(
                permission => permission.id === id
            );
            state.permissions.splice(index, 1);
        });
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
