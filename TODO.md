# Task: Perbaiki total_denda di Pengembalian Alat (Plan Approved)

## Plan Summary:
- Buat tabel returns dengan migration ✅
- Update AdminReturnController: hitung denda = hari_telat * 1000, simpan ke ReturnModel, gunakan ReturnModel di index
- Relasi & model sudah OK

## Steps:
- [x] 1. Create migration create_returns_table.php ✅
- [x] 2. php artisan migrate ✅ (DONE 394ms)
- [x] 3. Edit AdminReturnController.php ✅ (changes applied)

- [ ] 4. Test functionality
- [ ] 5. Complete - attempt_completion

Next: Controller edits.
