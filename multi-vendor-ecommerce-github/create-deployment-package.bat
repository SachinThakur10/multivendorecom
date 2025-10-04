@echo off
echo ========================================
echo  Multi-Vendor E-commerce Deployment
echo ========================================
echo.

echo Creating deployment package...
echo.

REM Create deployment folder
if not exist "deployment" mkdir deployment

REM Copy all necessary files
echo Copying PHP files...
xcopy "*.php" "deployment\" /Y /Q > nul
xcopy "api" "deployment\api\" /E /I /Y /Q > nul
xcopy "assets" "deployment\assets\" /E /I /Y /Q > nul
xcopy "config" "deployment\config\" /E /I /Y /Q > nul
xcopy "controllers" "deployment\controllers\" /E /I /Y /Q > nul
xcopy "database" "deployment\database\" /E /I /Y /Q > nul
xcopy "includes" "deployment\includes\" /E /I /Y /Q > nul
xcopy "models" "deployment\models\" /E /I /Y /Q > nul
xcopy "views" "deployment\views\" /E /I /Y /Q > nul

REM Create upload directories
echo Creating upload directories...
if not exist "deployment\assets\uploads\products" mkdir "deployment\assets\uploads\products"
if not exist "deployment\assets\uploads\vendors" mkdir "deployment\assets\uploads\vendors"
if not exist "deployment\assets\uploads\users" mkdir "deployment\assets\uploads\users"

REM Copy documentation
echo Copying documentation...
copy "README.md" "deployment\" > nul
copy "DEPLOYMENT.md" "deployment\" > nul
copy "INSTANT_DEPLOY.md" "deployment\" > nul
copy "QUICK_START.md" "deployment\" > nul

REM Copy production config template
copy "config\config-production.php" "deployment\config\" > nul

echo.
echo ========================================
echo  DEPLOYMENT PACKAGE READY!
echo ========================================
echo.
echo Your deployment package is in the 'deployment' folder
echo.
echo NEXT STEPS:
echo 1. Go to https://infinityfree.net/ and create account
echo 2. Upload all files from 'deployment' folder
echo 3. Create MySQL database and import schema.sql
echo 4. Update config/config.php with your database details
echo 5. Access your website!
echo.
echo Default Admin Login:
echo Email: admin@example.com
echo Password: admin123
echo.
echo Full instructions in INSTANT_DEPLOY.md
echo.
pause
