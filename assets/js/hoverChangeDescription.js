function hoverChangeDescription(nameCard, text) {
  var changeDescription = document.querySelector(".changeDescription");

  document.querySelector(nameCard).addEventListener("mouseover", () => {
    changeDescription.innerHTML = text;
  });

  document.querySelector(nameCard).addEventListener("mouseout", () => {
    changeDescription.innerHTML = `Feel free to move your mouse over any technology`;
  });
}

hoverChangeDescription(
  ".html",
  "Docker is an operating system for containers. In the same way that a virtual machine virtualizes server hardware (i.e., it is no longer necessary to manage it directly), containers virtualize the operating system of a server."
);
hoverChangeDescription(
  ".css",
  "Ansible is an Open Source software that allows to finely manage an IT infrastructure, automated multi-environment deployments, computers and system configurations."
);
hoverChangeDescription(
  ".js",
  "Windows PowerShell is an object-oriented automation engine and scripting language. With an interactive command interpreter, it is designed to help IT professionals configure systems and automate administration tasks."
);
hoverChangeDescription(
  ".sass",
  "Active Directory is Microsoft's implementation of LDAP directory services for Windows operating systems."
);
hoverChangeDescription(
  ".react",
  "VMware vSphere is a cloud computing infrastructure software from VMware, it is a type 1 hypervisor, based on the VMware ESXi architecture."
);
hoverChangeDescription(
  ".next",
  "MySQL is a relational database management system. It is distributed under a dual GPL and proprietary license."
);
hoverChangeDescription(
  ".styled",
  "In software engineering, agile practices emphasize collaboration between self-organized, multidisciplinary teams and their clients."
);
hoverChangeDescription(
  ".tailwind",
  "Git is a decentralized version control software. It is designed to be efficient with small projects as well as large ones."
);
