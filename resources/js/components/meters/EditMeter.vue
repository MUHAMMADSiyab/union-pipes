<template>
  <div>
    <Navbar v-if="!printMode" />

    <v-container class="mt-4">
      <v-row>
        <v-col cols="12">
          <v-card :loading="formLoading" :disabled="formLoading">
            <v-card-title primary-title>Edit Vehicle File</v-card-title>
            <v-card-subtitle>Edit the Vehicle File</v-card-subtitle>

            <v-card-text class="mt-1">
              <v-form @submit.prevent="update">
                <v-row>
                  <v-col cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('name')"
                    ></small>
                    <v-text-field
                      v-model="data.name"
                      label="Name"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('dispenser_id')"
                    ></small>
                    <v-select
                      :items="dispensers"
                      item-text="name"
                      item-value="id"
                      v-model="data.dispenser_id"
                      placeholder="Select Dispenser"
                      autocomplete
                      dense
                      outlined
                    ></v-select>
                  </v-col>

                  <v-col cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('code')"
                    ></small>
                    <v-text-field
                      v-model="data.code"
                      label="Code"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('description')"
                    ></small>
                    <v-textarea
                      rows="3"
                      name="description"
                      label="Description"
                      id="description"
                      v-model="data.description"
                      dense
                      outlined
                    ></v-textarea>
                  </v-col>
                </v-row>

                <v-btn color="primary" type="submit">Update</v-btn>
              </v-form>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <alert />
    </v-container>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../mixins/ValidationMixin";
import Navbar from "../navs/Navbar";

export default {
  mixins: [ValidationMixin],

  components: {
    Navbar,
  },

  data() {
    return {
      formLoading: false,
      data: {
        name: "",
        dispenser_id: "",
        code: "",
        description: "",
      },
    };
  },

  methods: {
    ...mapActions({
      getDispensers: "dispenser/getDispensers",
      getMeter: "meter/getMeter",
      updateMeter: "meter/updateMeter",
    }),

    async update() {
      this.formLoading = true;

      await this.updateMeter(this.data);

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        // Clear the validation messages object
        this.validation.setMessages({});

        // redirect to entries
        this.$router.push({ name: "meters" });
      }
    },
  },

  computed: {
    ...mapGetters({
      validationErrors: "validationErrors",
      dispensers: "dispenser/dispensers",
      meter: "meter/meter",
    }),
  },

  async mounted() {
    const meter_id = this.$route.params.id;

    await Promise.all([this.getDispensers(), this.getMeter(meter_id)]);

    if (!this.meter) {
      return this.$router.push({ name: "not_found" });
    }

    const { id, name, dispenser_id, code, description } = this.meter;

    this.data.id = id;
    this.data.name = name;
    this.data.dispenser_id = dispenser_id;
    this.data.code = code;
    this.data.description = description;
  },
};
</script>