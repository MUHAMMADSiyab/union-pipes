<template>
  <div>
    <Navbar v-if="!printMode" />

    <print-button />

    <v-container class="mt-4">
      <h5 class="text-subtitle-1 mb-2">Employee Salaries Records</h5>

      <EmployeeInfo :employee="employee" v-if="employee" />

      <Salaries
        :salaries="salaries"
        v-if="salaries"
        :totals="totals"
        :employee-id="employee ? employee.id : undefined"
      />
    </v-container>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import EmployeeInfo from "./partials/EmployeeInfo.vue";
import Navbar from "../navs/Navbar.vue";
import Salaries from "./partials/Salaries.vue";

export default {
  components: { Navbar, EmployeeInfo, Salaries },

  methods: {
    ...mapActions({
      getEmployee: "employee/getEmployee",
      getSalaries: "salary/getSalaries",
      getTotals: "salary/getTotals",
    }),
  },

  computed: {
    ...mapGetters({
      employee: "employee/employee",
      salaries: "salary/salaries",
      totals: "salary/totals",
    }),
  },

  mounted() {
    Promise.all([
      this.getEmployee(this.$route.params.employee_id),
      this.getSalaries(this.$route.params.employee_id),
      this.getTotals(this.$route.params.employee_id),
    ]);
  },
};
</script>