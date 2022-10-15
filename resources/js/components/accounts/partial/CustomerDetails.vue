<template>
  <v-card>
    <v-card-text>
      <v-row>
        <v-col xl="4" lg="4" md="4" sm="12">
          <v-img :src="customer.photo" height="120" width="120" rounded></v-img>
        </v-col>
        <v-col xl="8" lg="8" md="8" sm="12">
          <h5 class="text-uppercase light--text">Customer Details</h5>
          <h2 class="mt-2 mb-2">{{ customer.name }}</h2>
          <span class="d-block" v-if="customer.phone">
            <v-icon small left class="mr-2">mdi-cellphone-android</v-icon
            >{{ customer.phone }}
          </span>
          <span class="d-block" v-if="customer.cnic">
            <v-icon small left class="mr-2">mdi-card-account-details</v-icon
            >{{ customer.cnic }}
          </span>

          <span class="d-block" v-if="customer.address">
            <v-icon small left class="mr-2">mdi-google-maps</v-icon
            >{{ customer.address }}
          </span>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  props: ["customer"],
};
</script>