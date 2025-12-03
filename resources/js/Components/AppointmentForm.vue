<script setup>
    import DatePicker from 'primevue/datepicker'
    import { computed, onMounted, ref } from 'vue'

    import Select from 'primevue/select'
    import { Listbox, ListboxButton, ListboxOptions, ListboxOption } from '@headlessui/vue'
    import InputLabel from '@/Components/InputLabel.vue'
    import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'

    const props = defineProps({
        form: Object,
        genders: Array,
        priority_types: Array,
    })

    const filteredPriorityTypes = computed(() => {
        if (!props.form.gender) return props.priority_types

        // If gender is male → remove pregnant options
        if (props.form.gender.name.toLowerCase() === 'male') {
            return props.priority_types.filter((p) => p.code.toLowerCase() !== 'pw')
        }

        // Otherwise return all
        return props.priority_types
    })

    onMounted(() => {
        console.log("genders: ", props.genders)
        console.log("priority_types: ", props.priority_types)
    })
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
                    <input id="last_name" v-model="form.last_name" type="text" class="form-input" required />
                    <p v-if="form.errors.first_name" class="text-sm text-red-500 mt-1">
                        {{ form.errors.first_name }}
                    </p>
                </div>

                <div class="col-span-3">
                    <label for="email" class="block text-sm text-gray-900">Email</label>
                    <input id="email" v-model="form.email" type="email" class="form-input" required />
                    <p v-if="form.errors.email" class="text-sm text-red-500 mt-1">
                        {{ form.errors.email }}
                    </p>
                </div>

                <div class="col-span-3">
                    <label for="phone" class="block text-sm text-gray-900">Phone Number</label>
                    <div class="flex">
                        <span
                            class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm"
                        >
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

                <div class="col-span-3">
                    <label for="address" class="block text-sm text-gray-900">Address</label>
                    <input id="address" v-model="form.address" type="text" class="form-input" required />
                    <p v-if="form.errors.address" class="text-sm text-red-500 mt-1">
                        {{ form.errors.address }}
                    </p>
                </div>

                <!-- GENDER INPUT FORM -->
                <div class="col-span-3">
                    <InputLabel for="gender" value="Gender" />

                    <Listbox v-model="form.gender">
                        <div class="relative mt-1">
                            <ListboxButton
                                class="relative w-full cursor-default rounded-lg bg-white py-2 pl-3 pr-10 text-left shadow-md focus:outline-none focus-visible:border-indigo-500 focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-orange-300 sm:text-sm"
                            >
                                <span class="block truncate">{{ form.gender.name }}</span>
                                <span
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2"
                                >
                                    <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                                </span>
                            </ListboxButton>

                            <transition
                                leave-active-class="transition duration-100 ease-in"
                                leave-from-class="opacity-100"
                                leave-to-class="opacity-0"
                            >
                                <ListboxOptions
                                    class="absolute mt-1 max-h-60 z-50 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm"
                                >
                                    <ListboxOption
                                        v-slot="{ active, selected }"
                                        v-for="gender in props.genders"
                                        :key="gender.name"
                                        :value="gender"
                                        as="template"
                                    >
                                        <li
                                            :class="[
                                                active ? 'bg-green-100 text-green-900' : 'text-gray-900',
                                                'relative cursor-default select-none py-2 pl-10 pr-4',
                                            ]"
                                        >
                                            <span
                                                :class="[
                                                    selected ? 'font-medium' : 'font-normal',
                                                    'block truncate',
                                                ]"
                                            >
                                                {{ gender.name }}
                                            </span>
                                            <span
                                                v-if="selected"
                                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-green-600"
                                            >
                                                <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                            </span>
                                        </li>
                                    </ListboxOption>
                                </ListboxOptions>
                            </transition>
                        </div>
                    </Listbox>
                </div>

                <!-- LIST FOR PRIORITY TYPES -->
                <div class="col-span-3">
                    <InputLabel for="priority_type" value="Patient Type" />

                    <Listbox v-model="form.priority_type">
                        <div class="relative mt-1">
                            <ListboxButton
                                class="relative w-full cursor-default rounded-lg bg-white py-2 pl-3 pr-10 text-left shadow-md focus:outline-none focus-visible:border-indigo-500 focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-orange-300 sm:text-sm"
                            >
                                <span class="block truncate">{{ form.priority_type.name }}</span>
                                <span
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2"
                                >
                                    <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                                </span>
                            </ListboxButton>

                            <transition
                                leave-active-class="transition duration-100 ease-in"
                                leave-from-class="opacity-100"
                                leave-to-class="opacity-0"
                            >
                                <ListboxOptions
                                    class="absolute mt-1 max-h-60 z-50 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm"
                                >
                                    <ListboxOption
                                        v-slot="{ active, selected }"
                                        v-for="priority in filteredPriorityTypes"
                                        :key="priority.name"
                                        :value="priority"
                                        as="template"
                                    >
                                        <li
                                            :class="[
                                                active ? 'bg-green-100 text-green-900' : 'text-gray-900',
                                                'relative cursor-default select-none py-2 pl-10 pr-4',
                                            ]"
                                        >
                                            <span
                                                :class="[
                                                    selected ? 'font-medium' : 'font-normal',
                                                    'block truncate',
                                                ]"
                                            >
                                                {{ priority.name }}
                                            </span>
                                            <span
                                                v-if="selected"
                                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-green-600"
                                            >
                                                <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                            </span>
                                        </li>
                                    </ListboxOption>
                                </ListboxOptions>
                            </transition>
                        </div>
                    </Listbox>
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
