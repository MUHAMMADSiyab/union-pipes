<template>
  <v-card :loading="formLoading" :disabled="formLoading">
    <v-card-title primary-title>New Employee</v-card-title>
    <v-card-subtitle>Edit Employee</v-card-subtitle>

    <v-card-text class="mt-1">
      <v-form @submit.prevent="update">
        <v-row>
          <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
            <small
              class="red--text"
              v-if="validation.hasErrors()"
              v-text="validation.getMessage('name')"
            ></small>
            <v-text-field
              name="employee-name"
              label="Employee Name"
              id="employee-name"
              v-model="data.name"
              dense
              outlined
            ></v-text-field>
          </v-col>

          <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
            <small
              class="red--text"
              v-if="validation.hasErrors()"
              v-text="validation.getMessage('cnic')"
            ></small>
            <v-text-field
              name="employee-cnic"
              label="CNIC"
              id="employee-cnic"
              v-model="data.cnic"
              dense
              outlined
            ></v-text-field>
          </v-col>
        </v-row>

        <v-row>
          <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
            <small
              class="red--text"
              v-if="validation.hasErrors()"
              v-text="validation.getMessage('phone')"
            ></small>
            <v-text-field
              name="employee-phone"
              label="Phone"
              id="employee-phone"
              v-model="data.phone"
              dense
              outlined
            ></v-text-field>
          </v-col>

          <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
            <small
              class="red--text"
              v-if="validation.hasErrors()"
              v-text="validation.getMessage('photo')"
            ></small>
            <v-file-input
              name="employee-photo"
              label="Photo"
              id="employee-photo"
              @change="handleFile"
              prepend-inner-icon="mdi-camera"
              prepend-icon=""
              dense
              outlined
              hint="Only image files | Max. size 2MB"
              :clearable="false"
            ></v-file-input>
          </v-col>
        </v-row>

        <v-row>
          <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
            <small
              class="red--text"
              v-if="validation.hasErrors()"
              v-text="validation.getMessage('join_date')"
            ></small>
            <v-menu max-width="290px" min-width="auto">
              <template v-slot:activator="{ on }">
                <v-text-field
                  v-model="data.join_date"
                  v-on="on"
                  label="Join Date"
                  prepend-inner-icon="mdi-calendar"
                  dense
                  outlined
                ></v-text-field>
              </template>
              <v-date-picker
                v-model="data.join_date"
                label="Join Date"
                no-title
                outlined
                dense
                show-current
              ></v-date-picker>
            </v-menu>
          </v-col>

          <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
            <small
              class="red--text"
              v-if="validation.hasErrors()"
              v-text="validation.getMessage('salary')"
            ></small>
            <v-text-field
              type="number"
              name="employee-salary"
              label="Salary"
              id="employee-salary"
              v-model="data.salary"
              dense
              outlined
            ></v-text-field>
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12" class="py-0">
            <small
              class="red--text"
              v-if="validation.hasErrors()"
              v-text="validation.getMessage('address')"
            ></small>
            <v-textarea
              rows="2"
              name="employee-address"
              label="Address"
              id="employee-address"
              v-model="data.address"
              dense
              outlined
            ></v-textarea>
          </v-col>
        </v-row>

        <v-btn color="success" type="submit">Update</v-btn>
        <v-btn color="secondary" @click="closeDialog">Cancel</v-btn>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../mixins/ValidationMixin";

export default {
  props: ["singleEmployee"],

  mixins: [ValidationMixin],

  data() {
    const { id, name, cnic, phone, join_date, salary, address } =
      this.singleEmployee;

    return {
      formLoading: false,
      data: {
        id,
        name,
        cnic,
        phone,
        join_date,
        salary,
        address,
        photo: "",
      },
    };
  },

  methods: {
    ...mapActions({ updateEmployee: "employee/updateEmployee" }),

    handleFile(file) {
      this.data.photo = file;
    },

    async update() {
      this.formLoading = true;

      await this.updateEmployee(this.data);

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        // Clear the validation messages object
        this.validation.setMessages({});
        this.closeDialog();
      }
    },

    closeDialog() {
      this.$emit("closeDialog");
    },
  },

  watch: {
    singleEmployee: {
      handler({ id, name, cnic, phone, join_date, salary, address }) {
        this.data.id = id;
        this.data.name = name;
        this.data.cnic = cnic;
        this.data.phone = phone;
        this.data.join_date = join_date;
        this.data.salary = salary;
        this.data.address = address;
      },
    },
  },

  computed: {
    ...mapGetters({
      validationErrors: "validationErrors",
    }),
  },
};
</script>