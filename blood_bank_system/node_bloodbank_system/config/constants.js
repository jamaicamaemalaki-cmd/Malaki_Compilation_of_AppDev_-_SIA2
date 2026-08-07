const BLOOD_TYPES = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
const BLOOD_COMPONENTS = ['Whole Blood', 'Platelets', 'Plasma'];
const FACILITY_OPTIONS = {
  Hospital: ['Hinunangan Community Hospital', 'Zenon T. Lagumbay Memorial Hospital'],
  'Rural Health Unit': ['Hinunangan Rural Health Unit'],
  'Red Cross': ['Philippine Red Cross-Southern Leyte Chapter']
};

const FACILITY_NAMES = Object.values(FACILITY_OPTIONS).flat();

module.exports = {
  BLOOD_TYPES,
  BLOOD_COMPONENTS,
  FACILITY_OPTIONS,
  FACILITY_NAMES
};
