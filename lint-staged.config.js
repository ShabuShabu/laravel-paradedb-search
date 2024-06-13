export default {
  '{src,tests,config}/**/*.php': () => [
    'composer test-coverage',
    'composer type-coverage',
    'composer analyse',
    'composer format'
  ]
}