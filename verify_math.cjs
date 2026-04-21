const { execSync } = require('child_process');

const phpCode = `
require 'vendor/autoload.php';
$e = new \\Mammesat\\FilamentEthiopicDatePicker\\Services\\EthiopicCalendar();
$ethiopic = $e->getEthiopianDate(2024, 4, 10);
// Also calculate back to Gregorian
$gregorian = $e->getGregorianDate(2016, 8, 2);
echo json_encode(['toEth' => $ethiopic, 'toGreg' => $gregorian]);
`;
const phpResult = execSync(`php -r "${phpCode.replace(/"/g, '\\"')}"`).toString();
console.log("PHP Output:", phpResult);
