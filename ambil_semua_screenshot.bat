@echo off
cd /d "C:\xampp\htdocs\laravel\projek-kelas12\Velosha"
echo ========================================================
echo Memulai Pengambilan Screenshot Velosha (Playwright)...
echo ========================================================
"C:\nvm4w\nodejs\node.exe" scripts/capture_all_roles.mjs
pause

