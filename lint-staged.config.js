export default {
  '{src,tests,config}/**/*.php': () => [
    'composer test-coverage',
    'composer analyse',
    'composer format'
  ]
}