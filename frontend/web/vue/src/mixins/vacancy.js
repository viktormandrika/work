const vacancy = {
  data() {
    return {
      dataVacancy: [],
      lengthCompany: 0,
      formData: {
        vacancyCity: '',
        companyName: [],
        categoriesVacancy: [],
        post: '',
        duties: '',
        typeOfEmployment: null,
        salaryFrom: '',
        salaryBefore: '',
        qualificationRequirements: '',
        experience: '',
        education: '',
        workingConditions: '',
        vacancyVideo: '',
        officeAddress: '',
        houseNumber: '',
      },
      valid: false
    };
  }
};

export default vacancy;