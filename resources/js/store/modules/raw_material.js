import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_RAW_MATERIALS,
    UPDATE_RAW_MATERIAL,
    DELETE_RAW_MATERIAL,
    DELETE_RAW_MATERIALS,
    GET_RAW_MATERIAL,
    NEW_RAW_MATERIAL,
    GET_LEDGER_ENTRIES,
} from "../../mutation_constants";

const state = {
    raw_materials: [],
    raw_material: null,
    loading: false,
};

const getters = {
    raw_materials: (state) => state.raw_materials,
    raw_material: (state) => state.raw_material,
    loading: (state) => state.loading,
};

const actions = {
    // Add raw material
    async addRawMaterial({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/raw_materials", data);

            commit(NEW_RAW_MATERIAL, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: res.data.message,
                },
                { root: true }
            );
        } catch (error) {
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }

            console.log(error);
        }
    },

    // Get raw materials
    async getRawMaterials({ commit }) {
        try {
            const res = await axios.get("/api/raw_materials");

            commit(SET_LOADING, false, { root: true });
            commit(GET_RAW_MATERIALS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single raw material
    async getRawMaterial({ commit }, rawMaterialId) {
        try {
            const res = await axios.get(`/api/raw_materials/${rawMaterialId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_RAW_MATERIAL, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update raw material
    async updateRawMaterial({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/raw_materials/${data.id}`, data);

            commit(UPDATE_RAW_MATERIAL, res.data.raw_material);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: res.data.message,
                },
                { root: true }
            );
        } catch (error) {
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }
            console.log(error);
        }
    },

    // Delete raw material
    async deleteRawMaterial({ dispatch, commit }, rawMaterialId) {
        try {
            const res = await axios.delete(
                `/api/raw_materials/${rawMaterialId}`
            );

            commit(DELETE_RAW_MATERIAL, rawMaterialId);

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: res.data.message,
                },
                { root: true }
            );
        } catch (error) {
            console.log(error);
        }
    },

    // Delete multiple raw materials
    async deleteMultipleRawMaterials({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(
                `/api/raw_materials/delete_multiple`,
                {
                    headers: {},
                    data: { ids },
                }
            );

            commit(DELETE_RAW_MATERIALS, ids);

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: res.data.message,
                },
                { root: true }
            );
        } catch (error) {
            console.log(error);
        }
    },
};

const mutations = {
    GET_RAW_MATERIALS: (state, payload) => (state.raw_materials = payload),

    GET_RAW_MATERIAL: (state, payload) => (state.raw_material = payload),

    GET_LEDGER_ENTRIES: (state, payload) => (state.ledger_entries = payload),

    NEW_RAW_MATERIAL: (state, payload) => {
        state.raw_materials.unshift(payload);
    },

    UPDATE_RAW_MATERIAL: (state, payload) => {
        const index = state.raw_materials.findIndex(
            (raw_material) => raw_material.id === payload.id
        );
        state.raw_materials.splice(index, 1, payload);
    },

    DELETE_RAW_MATERIAL: (state, payload) => {
        const index = state.raw_materials.findIndex(
            (raw_material) => raw_material.id === payload
        );
        state.raw_materials.splice(index, 1);
    },

    DELETE_RAW_MATERIALS: (state, payload) => {
        payload.forEach((id) => {
            const index = state.raw_materials.findIndex(
                (raw_material) => raw_material.id === id
            );
            state.raw_materials.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
