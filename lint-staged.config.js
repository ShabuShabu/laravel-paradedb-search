export default {
  '{src,tests,config}/**/*.php': () => [
    'composer test-coverage',
    'composer format'
  ]
}