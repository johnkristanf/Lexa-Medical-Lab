<script setup>
    import DatePicker from 'primevue/datepicker'
    import { ref } from 'vue'

    import Select from 'primevue/select'

    const props = defineProps({
        form: Object,
    })

    const genders = ref([
        { name: 'Male', tag: 'male' },
        { name: 'Female', tag: 'female' },
    ])
</script>

<template>
    <div class="isolate p-5">
        <div class="flex flex-col mb-8">
            <h1 class="text-3xl font-bold">Personal Information</h1>
            <p class="text-gray-500 text-sm">Please provide accurate information</p>
        </div>

        <form @submit.prevent="submitForm" class="max-w-xl">
            <div class="grid grid-cols-2 gap-x-8 gap-y-6 sm:grid-cols-3">
                <div>
                    <label for="first_name" class="block text-sm text-gray-900">First Name</label>
                    <input
                        id="first_name"
                        v-model="form.first_name"
                        type="text"
                        class="form-input"
                        required
                    />
                    <p v-if="form.errors.first_name" class="text-sm text-red-500 mt-1">
                        {{ form.errors.first_name }}
                    </p>
                </div>

                <div>
                    <label for="middle_name" class="block text-sm text-gray-900">
                        Middle Name (optional)
                    </label>
                    <input
                        id="first_name"
                        v-model="form.middle_name"
                        type="text"
                        class="form-input"
                        required
                    />
                    <p v-if="form.errors.first_name" class="text-sm text-red-500 mt-1">
                        {{ form.errors.first_name }}
                    </p>
                </div>

                <div>
                    <label for="last_name" class="block text-sm text-gray-900">Last Name</label>
                    <input
                        id="last_name"
                        v-model="form.last_name"
                        type="text"
                        class="form-input"
                        required
                    />
                    <p v-if="form.errors.first_name" class="text-sm text-red-500 mt-1">
                        {{ form.errors.first_name }}
                    </p>
                </div>

                <div class="col-span-3">
                    <label for="email" class="block text-sm text-gray-900">Email</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="form-input"
                        required
                    />
                    <p v-if="form.errors.email" class="text-sm text-red-500 mt-1">
                        {{ form.errors.email }}
                    </p>
                </div>

                <div class="col-span-3">
                    <label for="phone" class="block text-sm text-gray-900">Phone Number</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                            +63
                        </span>
                        <input
                            id="phone"
                            v-model="form.phone"
                            type="text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            class="form-input rounded-l-none"
                            placeholder="9123456789"
                            required
                        />
                    </div>
                    <p v-if="form.errors.phone" class="text-sm text-red-500 mt-1">
                        {{ form.errors.phone }}
                    </p>
                </div>

                <!-- GENDER INPUT FORM -->
                <div class="col-span-3">
                    <label for="gender" class="block text-sm text-gray-900">Sex</label>

                    <Select
                        v-model="form.gender"
                        :options="genders"
                        optionLabel="name"
                        optionValue="tag"
                        class="w-full"
                    />
                </div>

                <!-- DATE PICKER INPUT FORM -->
                <div class="col-span-3">
                    <label for="icondisplay" class="block mb-2">Birth Date</label>
                    <DatePicker
                        v-model="form.birthdate"
                        showIcon
                        fluid
                        iconDisplay="input"
                        inputId="icondisplay"
                    />
                </div>
            </div>
        </form>
    </div>
</template>
