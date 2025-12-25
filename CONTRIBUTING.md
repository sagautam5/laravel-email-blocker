# Contributing

Contributions are **welcome** and will be fully **credited**.

Please read and understand this contribution guide before creating an issue or submitting a pull request.

---

## Etiquette

This project is open source, and the maintainers dedicate their personal time to build and maintain it.
The code is shared freely in the hope that it will be useful to the community.

Please be respectful and considerate when raising issues or submitting pull requests.
Abusive, demanding, or disrespectful behavior will not be tolerated.

Maintainers are responsible for ensuring that all contributions meet the quality standards of the project.
Not all submissions can be accepted. Please respect maintainers’ decisions, even if your contribution is not merged.

---

## Viability

Before proposing a new feature or enhancement, consider whether it will be useful to a broad audience.

Open source projects are used by developers with diverse requirements.
Features that solve highly specific or niche problems may not be accepted unless they are extensible and broadly applicable.

---

## Procedure

### Before Filing an Issue

- Try to reproduce the issue to ensure it is not a one-off or environment-specific problem.
- Check existing issues to avoid duplicates.
- Review open pull requests to see if a fix or feature is already in progress.
- Clearly describe the problem, expected behavior, and actual behavior.

---

### Before Submitting a Pull Request

- Install dependencies by running the following command from the project root:

```sh
composer install
```

- Check the pull requests tab to ensure that the bug doesn't have a fix in progress.

- Create and test the feature/issue and make sure it doesn't affect codebase by running full tests by executing following console command for two different environment variables.

```sh
composer test
```

- Make sure contribution align with phpstan standards. You can run following command to ensure this.
```sh  
composer phpstan
```
- Make sure new addition is well formatted by running following command.

```sh 
composer format
```
- Check the codebase to ensure that your feature doesn't already exist.
- Check the pull requests to ensure that another person hasn't already submitted the feature or fix.


## Requirements
If the project maintainer has any additional requirements, you will find them listed here.

- Document any change in behaviour - Make sure the README.md and any other relevant documentation are kept up-to-date.

- One pull request per feature - If you want to do more than one thing, send multiple pull requests.

- Send coherent history - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please squash them before submitting.

Happy coding !

