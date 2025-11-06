# 🔐 Security Guidelines

## ⚠️ URGENT: Credential Rotation Required

If you have committed or pushed the `.env` file with real credentials to any git repository (local or remote), you **MUST** rotate all sensitive credentials immediately.

### Leaked Credentials Detected

The following credentials were found in the codebase and may have been exposed:

- **MAIL_PASSWORD**: Gmail App Password
- **MAIL_USERNAME**: Email address
- **DB_PASSWORD**: Database password

### Immediate Actions Required

#### 1. Rotate Gmail App Password

1. Go to https://myaccount.google.com/apppasswords
2. **Revoke** the existing App Password that was exposed
3. Generate a **new** App Password
4. Update your local `.env` file with the new password:
   ```
   MAIL_PASSWORD=new-16-char-password
   ```

#### 2. Rotate Database Password (if production)

If `DB_PASSWORD` in your `.env` is a production database password:

1. Access your PostgreSQL database
2. Change the password for the user:
   ```sql
   ALTER USER your_username WITH PASSWORD 'new-secure-password';
   ```
3. Update your local `.env` file:
   ```
   DB_PASSWORD=new-secure-password
   ```

#### 3. Check Git History

Check if `.env` was ever committed to git:

```bash
git log --all --full-history -- .env
```

If you see commits, you need to:

**Option A: If remote repository is private and has few collaborators**
- Notify all collaborators to rotate their credentials
- Consider this a minor exposure

**Option B: If remote repository is public or widely shared**
- Treat all credentials as compromised
- Rotate ALL secrets in `.env`
- Consider purging git history (destructive - requires coordination)

#### 4. Purge Git History (Optional - Advanced)

⚠️ **WARNING**: This rewrites git history and requires force-pushing. Coordinate with your team first.

```bash
# Remove .env from all commits
git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch .env" \
  --prune-empty --tag-name-filter cat -- --all

# Force push (dangerous!)
git push origin --force --all
git push origin --force --tags
```

Alternatively, use `git-filter-repo` (recommended):
```bash
git filter-repo --invert-paths --path .env
```

---

## 🛡️ Prevention: Best Practices

### 1. Never Commit `.env`

The `.env` file is already in `.gitignore`. Verify:

```bash
cat .gitignore | grep .env
```

You should see:
```
.env
.env.backup
.env.production
```

### 2. Use Pre-Commit Hooks

Install a pre-commit hook to prevent accidental commits:

```bash
# Create pre-commit hook
cat > .git/hooks/pre-commit << 'EOF'
#!/bin/sh
if git diff --cached --name-only | grep -q "^\.env$"; then
    echo "ERROR: Attempting to commit .env file!"
    echo "This file contains sensitive credentials and should not be committed."
    exit 1
fi
EOF

# Make it executable
chmod +x .git/hooks/pre-commit
```

For Windows (PowerShell):
```powershell
# Create pre-commit hook
@"
#!/bin/sh
if git diff --cached --name-only | grep -q "^\.env$"; then
    echo "ERROR: Attempting to commit .env file!"
    echo "This file contains sensitive credentials and should not be committed."
    exit 1
fi
"@ | Out-File -FilePath .git/hooks/pre-commit -Encoding ASCII

# Windows doesn't need chmod, but ensure Git recognizes it
git config core.hooksPath .git/hooks
```

### 3. Use Environment-Specific Files

For different environments, use separate files:

- `.env.example` - Template with placeholder values (safe to commit)
- `.env` - Local development (never commit)
- `.env.testing` - Testing environment (never commit)
- `.env.production` - Production (never commit, use secure storage)

### 4. Secure Production Credentials

For production, **never** store credentials in files. Use:

- **Environment Variables**: Set via server/container config
- **Secret Managers**: Azure Key Vault, AWS Secrets Manager, HashiCorp Vault
- **CI/CD Secrets**: GitHub Secrets, GitLab CI/CD Variables
- **Encrypted Storage**: Laravel Encrypted Env, dotenv-vault

Example using environment variables:
```bash
# Set in server config or container
export MAIL_PASSWORD=actual-app-password
export DB_PASSWORD=actual-db-password
```

### 5. Regular Security Audits

Run periodic scans for secrets:

```bash
# Check for common secret patterns
git log -p | grep -E "(password|secret|key|token|api)" -i

# Use automated tools
npm install -g gitleaks
gitleaks detect --source . --verbose
```

---

## 📋 Credential Checklist

Use this checklist after a credential leak:

- [ ] Rotated Gmail App Password
- [ ] Rotated Database Password (if production)
- [ ] Updated local `.env` file
- [ ] Checked git history for `.env` commits
- [ ] Notified team members (if applicable)
- [ ] Purged git history (if necessary)
- [ ] Installed pre-commit hooks
- [ ] Verified `.env` is in `.gitignore`
- [ ] Enabled 2FA on all services
- [ ] Reviewed access logs for suspicious activity
- [ ] Updated documentation to reflect new security practices

---

## 🆘 Emergency Contacts

If you suspect a security breach:

1. **Immediately** revoke all compromised credentials
2. Check access logs for unauthorized access
3. Notify your security team or project maintainer
4. Document the incident for post-mortem review

---

## 📚 Additional Resources

- [OWASP Secrets Management Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Secrets_Management_Cheat_Sheet.html)
- [GitHub Secret Scanning](https://docs.github.com/en/code-security/secret-scanning/about-secret-scanning)
- [Git-secrets Tool](https://github.com/awslabs/git-secrets)
- [Laravel Security Best Practices](https://laravel.com/docs/11.x/deployment#security)

---

**Last Updated**: November 7, 2025
