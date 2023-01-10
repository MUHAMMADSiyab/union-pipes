<template>
  <v-row>
    <v-col cols="12">
      <v-card>
        <v-card-title primary-title>{{ employee.name }}</v-card-title>
        <v-card-subtitle>Employee's Details</v-card-subtitle>

        <v-card-text class="mt-1">
          <v-row>
            <v-col xl="3" lg="3" md="3" sm="3" cols="12" class="py-0">
              <v-list dense>
                <v-list-item>
                  <v-list-item-content>
                    <v-list-item-content>
                      <strong>Employee's Name</strong>
                      {{ employee.name }}
                    </v-list-item-content>
                  </v-list-item-content>
                </v-list-item>

                <v-list-item>
                  <v-list-item-content>
                    <v-list-item-content>
                      <strong>Contact Number</strong>
                      {{ employee.phone }}
                    </v-list-item-content>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-col>

            <v-col xl="3" lg="3" md="3" sm="3" cols="12" class="py-0">
              <v-list dense>
                <v-list-item>
                  <v-list-item-content>
                    <v-list-item-content>
                      <strong>CNIC</strong>
                      {{ employee.cnic }}
                    </v-list-item-content>
                  </v-list-item-content>
                </v-list-item>

                <v-list-item>
                  <v-list-item-content>
                    <v-list-item-content>
                      <strong>Address</strong>
                      {{ employee.address }}
                    </v-list-item-content>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-col>

            <v-col xl="3" lg="3" md="3" sm="3" cols="12" class="py-0">
              <v-list dense>
                <v-list-item>
                  <v-list-item-content>
                    <v-list-item-content>
                      <strong>Join Date</strong>
                      {{ employee.join_date }}
                    </v-list-item-content>
                  </v-list-item-content>
                </v-list-item>

                <v-list-item>
                  <v-list-item-content>
                    <v-list-item-content>
                      <strong>Salary</strong>
                      {{ employee.salary }}
                    </v-list-item-content>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-col>

            <v-col xl="2" lg="2" md="2" sm="12" cols="12" class="py-0">
              <v-img
                :src="employee.photo"
                max-width="150"
                max-height="150"
                class="rounded-lg"
              ></v-img>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: ["employee"],
};
</script>