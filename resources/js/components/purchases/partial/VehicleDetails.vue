<template>
  <v-card v-if="vehicle">
    <v-card-title primary-title>
      <h6 class="text-uppercase">Vehicle Details</h6>
    </v-card-title>
    <v-card-text>
      <v-simple-table dense>
        <template v-slot:default>
          <tbody>
            <tr>
              <td>Make</td>
              <td>{{ vehicle.make }}</td>
            </tr>
            <tr>
              <td>Model</td>
              <td>{{ vehicle.model }}</td>
            </tr>
            <tr v-if="vehicle.chassis_no">
              <td>Chassis No.</td>
              <td>{{ vehicle.chassis_no }}</td>
            </tr>
            <tr v-if="vehicle.registration_no">
              <td>Registration No.</td>
              <td>{{ vehicle.registration_no }}</td>
            </tr>
            <tr v-if="vehicle.engine_no">
              <td>Engine No.</td>
              <td>{{ vehicle.engine_no }}</td>
            </tr>
            <tr v-if="vehicle.capacity">
              <td>Capacity</td>
              <td>{{ vehicle.capacity }}</td>
            </tr>
            <tr v-if="vehicle.contractor_name">
              <td>Contractor Name</td>
              <td>{{ vehicle.contractor_name }}</td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </v-card-text>
  </v-card>
</template>
  
  <script>
export default {
  props: ["vehicle"],
};
</script>