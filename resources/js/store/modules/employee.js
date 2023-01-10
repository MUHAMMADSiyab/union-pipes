import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    NEW_EMPLOYEE,
    GET_EMPLOYEES,
    GET_EMPLOYEE,
    UPDATE_EMPLOYEE,
    DELETE_EMPLOYEE,
    DELETE_EMPLOYEES,
} from "../../mutation_constants";

const state = {
    employees: [],
    employee: null,
};

const getters = {
    employees: (state) => state.employees,
    employee: (state) => state.employee,
};

const actions = {
    // Add employee
    async addEmployee(
        { dispatch, commit },
        { name, cnic, phone, address, join_date, salary, photo }
    ) {
        try {
            const fd = new FormData();

            fd.append("name", name);
            fd.append("cnic", cnic);
            fd.append("phone", phone);
            fd.append("address", address);
            fd.append("join_date", join_date);
            fd.append("salary", salary);
            fd.append("photo", photo);

            const res = await axios.post("/api/employees", fd);

            commit(NEW_EMPLOYEE, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Employee added successfully",
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

    // Get employees
    async getEmployees({ commit }) {
        try {
            const res = await axios.get("/api/employees");

            commit(SET_LOADING, false, { root: true });
            commit(GET_EMPLOYEES, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single employee
    async getEmployee({ commit }, employeeId) {
        try {
            const res = await axios.get(`/api/employees/${employeeId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_EMPLOYEE, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update employee
    async updateEmployee(
        { dispatch, commit },
        { id, name, cnic, phone, address, join_date, salary, photo }
    ) {
        try {
            const fd = new FormData();

            fd.append("name", name);
            fd.append("cnic", cnic);
            fd.append("phone", phone);
            fd.append("address", address);
            fd.append("join_date", join_date);
            fd.append("salary", salary);
            fd.append("photo", photo);
            fd.append("_method", "PUT");

            const res = await axios.post(`/api/employees/${id}`, fd);

            commit(UPDATE_EMPLOYEE, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Employee updated successfully",
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

    // Delete employee
    async deleteEmployee({ dispatch, commit }, employeeId) {
        try {
            const res = await axios.delete(`/api/employees/${employeeId}`);

            commit(DELETE_EMPLOYEE, employeeId);

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: res.data.success,
                },
                { root: true }
            );
        } catch (error) {
            console.log(error);
        }
    },

    // Delete multiple employees
    async deleteMultipleEmployees({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/employees/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_EMPLOYEES, ids);

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: res.data.success,
                },
                { root: true }
            );
        } catch (error) {
            console.log(error);
        }
    },
};

const mutations = {
    GET_EMPLOYEES: (state, payload) => (state.employees = payload),

    GET_EMPLOYEE: (state, payload) => (state.employee = payload),

    NEW_EMPLOYEE: (state, payload) => {
        state.employees.unshift(payload);
    },

    UPDATE_EMPLOYEE: (state, payload) => {
        const index = state.employees.findIndex(
            (employee) => employee.id === payload.id
        );
        state.employees.splice(index, 1, payload);
    },

    DELETE_EMPLOYEE: (state, payload) => {
        const index = state.employees.findIndex(
            (employee) => employee.id === payload
        );
        state.employees.splice(index, 1);
    },

    DELETE_EMPLOYEES: (state, payload) => {
        payload.forEach((id) => {
            const index = state.employees.findIndex(
                (employee) => employee.id === id
            );
            state.employees.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
