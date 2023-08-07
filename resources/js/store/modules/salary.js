import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_SALARIES,
    UPDATE_SALARY,
    DELETE_SALARY,
    GET_SALARY,
    NEW_SALARY,
    OLD_SALARY,
    GET_SALARY_TOTALS,
} from "../../mutation_constants";

const state = {
    salaries: [],
    totals: [],
    salary: null,
    recent_salary: null,
    old_salary: null,
};

const getters = {
    salaries: (state) => state.salaries,
    salary: (state) => state.salary,
    recent_salary: (state) => state.recent_salary,
    old_salary: (state) => state.old_salary,
    totals: (state) => state.totals,
};

const actions = {
    // Add salary
    async addSalary({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/salaries", data);

            commit(NEW_SALARY, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Salary payment added successfully",
                },
                { root: true }
            );
        } catch (error) {
            if (error.response.status === 400) {
                return dispatch(
                    "alert/setAlert",
                    {
                        type: "error",
                        message: error.response.data.duplicate_error,
                    },
                    { root: true }
                );
            } else {
                if (error.response.status === 422) {
                    commit(SET_VALIDATION_ERRORS, error.response.data, {
                        root: true,
                    });
                }
            }

            console.log(error);
        }
    },

    // Get salaries
    async getSalaries({ commit }, { loan, employeeId }) {
        try {
            const res = await axios.get(
                `/api/salaries/${employeeId}/get_records?loan=${loan}`
            );

            commit(SET_LOADING, false, { root: true });
            commit(GET_SALARIES, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get salaries totals
    async getTotals({ commit }, { loan, employeeId }) {
        try {
            const res = await axios.get(
                `/api/salaries/${employeeId}/get_totals?loan=${loan}`
            );

            commit(SET_LOADING, false, { root: true });
            commit(GET_SALARY_TOTALS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single salary
    async getSalary({ commit }, salaryId) {
        try {
            const res = await axios.get(`/api/salaries/${salaryId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_SALARY, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update salary
    async updateSalary({ commit, dispatch }, data) {
        try {
            const res = await axios.put(`/api/salaries/${data.id}`, data);

            commit(UPDATE_SALARY, res.data.updated_salary);
            commit(OLD_SALARY, res.data.old_salary);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Salary payment updated successfully",
                },
                { root: true }
            );
        } catch (error) {
            console.log(error);

            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }
            console.log(error);
        }
    },

    // Delete salary
    async deleteSalary({ dispatch, commit }, { id, employeeId }) {
        try {
            const res = await axios.delete(`/api/salaries/${id}`);

            commit(DELETE_SALARY, id);
            commit(GET_SALARY_TOTALS, employeeId);

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
    GET_SALARIES: (state, payload) => (state.salaries = payload),

    GET_SALARY_TOTALS: (state, payload) => (state.totals = payload),

    GET_SALARY: (state, payload) => (state.salary = payload),

    NEW_SALARY: (state, payload) => {
        state.recent_salary = payload;
    },

    PAYMENT: (state, payload) => {
        const index = state.salaries.findIndex(
            (salary) => salary.id === payload.id
        );

        state.salaries.splice(index, 1, payload);
    },

    OLD_SALARY: (state, payload) => {
        state.old_salary = payload;
    },

    UPDATE_SALARY: (state, payload) => {
        const index = state.salaries.findIndex(
            (salary) => salary.id === payload.id
        );
        state.salaries.splice(index, 1, payload);
    },

    DELETE_SALARY: (state, payload) => {
        const index = state.salaries.findIndex(
            (salary) => salary.id === payload
        );
        state.salaries.splice(index, 1);
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
